<?php

declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../faturas/_sync.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJson(['success' => false, 'message' => 'Método não permitido.'], 405);
}

$data = getRequestData();
$id = isset($data['id']) ? (int) $data['id'] : 0;

if ($id <= 0) {
    sendJson(['success' => false, 'message' => 'ID inválido.'], 422);
}

$pdo = getPdo();

try {
    $pdo->beginTransaction();

    $registroStmt = $pdo->prepare('SELECT id, paciente_id, medicamento_id, quantidade FROM registros_uso WHERE id = :id LIMIT 1 FOR UPDATE');
    $registroStmt->execute(['id' => $id]);
    $registro = $registroStmt->fetch();

    if (!$registro) {
        throw new RuntimeException('Registro não encontrado.');
    }

    $medicamentoStmt = $pdo->prepare('SELECT id, estoque FROM medicamentos WHERE id = :id LIMIT 1 FOR UPDATE');
    $medicamentoStmt->execute(['id' => (int) $registro['medicamento_id']]);
    $medicamento = $medicamentoStmt->fetch();

    if (!$medicamento) {
        throw new RuntimeException('Medicamento não encontrado.');
    }

    $novoEstoque = (int) $medicamento['estoque'] + (int) $registro['quantidade'];

    $update = $pdo->prepare('UPDATE medicamentos SET estoque = :estoque WHERE id = :id');
    $update->execute([
        'estoque' => $novoEstoque,
        'id' => (int) $medicamento['id'],
    ]);

    $delete = $pdo->prepare('DELETE FROM registros_uso WHERE id = :id');
    $delete->execute(['id' => $id]);

    sincronizarFaturas($pdo, (int) $registro['paciente_id']);

    $medicamentoNovoStmt = $pdo->prepare('SELECT id, estoque FROM medicamentos WHERE id = :id LIMIT 1');
    $medicamentoNovoStmt->execute(['id' => (int) $medicamento['id']]);

    $pdo->commit();

    sendJson([
        'success' => true,
        'message' => 'Registro removido com sucesso.',
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