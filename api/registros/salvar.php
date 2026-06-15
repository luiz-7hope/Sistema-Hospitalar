<?php

declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../faturas/_sync.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJson(['success' => false, 'message' => 'Método não permitido.'], 405);
}

$data = getRequestData();

$pacienteId = isset($data['paciente_id']) ? (int) $data['paciente_id'] : 0;
$medicamentoId = isset($data['medicamento_id']) ? (int) $data['medicamento_id'] : 0;
$quantidade = isset($data['quantidade']) ? (int) $data['quantidade'] : 0;
$dataUso = trim((string) ($data['data_uso'] ?? ''));
$observacoes = trim((string) ($data['observacoes'] ?? ''));
$usuarioId = isset($data['usuario_id']) && $data['usuario_id'] !== '' ? (int) $data['usuario_id'] : null;

if ($pacienteId <= 0 || $medicamentoId <= 0 || $quantidade <= 0 || $dataUso === '') {
    sendJson(['success' => false, 'message' => 'Preencha todos os campos obrigatórios.'], 422);
}

$dataUso = str_replace('T', ' ', $dataUso);
if (strlen($dataUso) === 16) {
    $dataUso .= ':00';
}

$pdo = getPdo();

try {
    $pdo->beginTransaction();

    $pacienteStmt = $pdo->prepare('SELECT id FROM pacientes WHERE id = :id LIMIT 1');
    $pacienteStmt->execute(['id' => $pacienteId]);
    if (!$pacienteStmt->fetch()) {
        throw new RuntimeException('Paciente não encontrado.');
    }

    $medicamentoStmt = $pdo->prepare('SELECT id, nome, preco_unitario, estoque FROM medicamentos WHERE id = :id LIMIT 1 FOR UPDATE');
    $medicamentoStmt->execute(['id' => $medicamentoId]);
    $medicamento = $medicamentoStmt->fetch();

    if (!$medicamento) {
        throw new RuntimeException('Medicamento não encontrado.');
    }

    if ((int) $medicamento['estoque'] < $quantidade) {
        throw new RuntimeException('Estoque insuficiente.');
    }

    $novoEstoque = (int) $medicamento['estoque'] - $quantidade;
    $precoTotal = (float) $medicamento['preco_unitario'] * $quantidade;

    $update = $pdo->prepare('UPDATE medicamentos SET estoque = :estoque WHERE id = :id');
    $update->execute([
        'estoque' => $novoEstoque,
        'id' => $medicamentoId,
    ]);

    $insert = $pdo->prepare(
        'INSERT INTO registros_uso (paciente_id, medicamento_id, quantidade, preco_total, data_uso, observacoes, usuario_id) VALUES (:paciente_id, :medicamento_id, :quantidade, :preco_total, :data_uso, :observacoes, :usuario_id)'
    );
    $insert->execute([
        'paciente_id' => $pacienteId,
        'medicamento_id' => $medicamentoId,
        'quantidade' => $quantidade,
        'preco_total' => $precoTotal,
        'data_uso' => $dataUso,
        'observacoes' => $observacoes !== '' ? $observacoes : null,
        'usuario_id' => $usuarioId,
    ]);

    $registroId = (int) $pdo->lastInsertId();

    sincronizarFaturas($pdo, $pacienteId, $usuarioId);

    $registroStmt = $pdo->prepare(
        'SELECT r.id, r.paciente_id, p.nome AS paciente_nome, r.medicamento_id, m.nome AS medicamento_nome, r.quantidade, r.preco_total, r.data_uso, r.observacoes, r.data_cadastro, r.usuario_id
         FROM registros_uso r
         INNER JOIN pacientes p ON p.id = r.paciente_id
         INNER JOIN medicamentos m ON m.id = r.medicamento_id
         WHERE r.id = :id LIMIT 1'
    );
    $registroStmt->execute(['id' => $registroId]);

    $medicamentoNovoStmt = $pdo->prepare('SELECT id, estoque FROM medicamentos WHERE id = :id LIMIT 1');
    $medicamentoNovoStmt->execute(['id' => $medicamentoId]);

    $pdo->commit();

    sendJson([
        'success' => true,
        'message' => 'Uso registrado com sucesso.',
        'registro' => $registroStmt->fetch(),
        'medicamento' => $medicamentoNovoStmt->fetch(),
    ]);
} catch (Throwable $exception) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    sendJson([
        'success' => false,
        'message' => $exception->getMessage(),
    ], 422);
}