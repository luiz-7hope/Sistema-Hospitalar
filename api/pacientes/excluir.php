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

$stmt = $pdo->prepare('DELETE FROM pacientes WHERE id = :id');
$stmt->execute(['id' => $id]);

if ($stmt->rowCount() === 0) {
    sendJson(['success' => false, 'message' => 'Paciente não encontrado.'], 404);
}

sendJson([
    'success' => true,
    'message' => 'Paciente removido com sucesso.',
]);