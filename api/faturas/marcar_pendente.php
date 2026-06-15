<?php

declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/_sync.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJson(['success' => false, 'message' => 'Método não permitido.'], 405);
}

$data = getRequestData();
$id = isset($data['id']) ? (int) $data['id'] : 0;
$usuarioId = isset($data['usuario_id']) && $data['usuario_id'] !== '' ? (int) $data['usuario_id'] : null;

if ($id <= 0) {
    sendJson(['success' => false, 'message' => 'ID inválido.'], 422);
}

$pdo = getPdo();

try {
    $pdo->beginTransaction();

    $faturaStmt = $pdo->prepare('SELECT id, paciente_id FROM faturas WHERE id = :id LIMIT 1 FOR UPDATE');
    $faturaStmt->execute(['id' => $id]);
    $fatura = $faturaStmt->fetch();

    if (!$fatura) {
        throw new RuntimeException('Fatura não encontrada.');
    }

    $update = $pdo->prepare(
        'UPDATE faturas
         SET status = :status, data_pagamento = NULL, usuario_id = :usuario_id
         WHERE paciente_id = :paciente_id'
    );
    $update->execute([
        'status' => 'Pendente',
        'usuario_id' => $usuarioId,
        'paciente_id' => (int) $fatura['paciente_id'],
    ]);

    limparFaturasDuplicadas($pdo, (int) $fatura['paciente_id']);

    $faturaAtualizadaStmt = $pdo->prepare(
        'SELECT f.id, f.paciente_id, p.nome AS paciente_nome, p.leito, p.plano_saude, f.valor_total, f.status, f.data_emissao, f.data_pagamento, f.usuario_id
         FROM faturas f
         INNER JOIN pacientes p ON p.id = f.paciente_id
         WHERE f.paciente_id = :paciente_id
         ORDER BY f.id DESC
         LIMIT 1'
    );
    $faturaAtualizadaStmt->execute(['paciente_id' => (int) $fatura['paciente_id']]);

    $pdo->commit();

    sendJson([
        'success' => true,
        'message' => 'Fatura marcada como pendente.',
        'fatura' => $faturaAtualizadaStmt->fetch(),
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