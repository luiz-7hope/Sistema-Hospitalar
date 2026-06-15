<?php

declare(strict_types=1);

require_once __DIR__ . '/../config.php';

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
    $stmt = $pdo->prepare('DELETE FROM medicamentos WHERE id = :id');
    $stmt->execute(['id' => $id]);

    if ($stmt->rowCount() === 0) {
        sendJson(['success' => false, 'message' => 'Medicamento não encontrado.'], 404);
    }
} catch (PDOException $exception) {
    sendJson([
        'success' => false,
        'message' => 'Não foi possível remover o medicamento. Ele pode estar vinculado a registros de uso.',
        'error' => $exception->getMessage(),
    ], 409);
}

sendJson([
    'success' => true,
    'message' => 'Medicamento removido com sucesso.',
]);