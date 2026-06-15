<?php

declare(strict_types=1);

require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendJson(['success' => false, 'message' => 'Método não permitido.'], 405);
}

$pdo = getPdo();

$stmt = $pdo->query(
    'SELECT id, nome, cpf, plano_saude, leito, data_internacao, status, data_cadastro, usuario_id FROM pacientes ORDER BY id DESC'
);

sendJson([
    'success' => true,
    'pacientes' => $stmt->fetchAll(),
]);