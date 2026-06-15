<?php

declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/_sync.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJson(['success' => false, 'message' => 'Método não permitido.'], 405);
}

$data = getRequestData();
$pacienteId = isset($data['paciente_id']) ? (int) $data['paciente_id'] : null;
$usuarioId = isset($data['usuario_id']) && $data['usuario_id'] !== '' ? (int) $data['usuario_id'] : null;

$pdo = getPdo();

try {
    $pdo->beginTransaction();
    sincronizarFaturas($pdo, $pacienteId, $usuarioId);
    $pdo->commit();

    sendJson([
        'success' => true,
        'message' => 'Faturas sincronizadas com sucesso.',
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