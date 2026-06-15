<?php

declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/_sync.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendJson(['success' => false, 'message' => 'Método não permitido.'], 405);
}

$pdo = getPdo();

try {
    $pdo->beginTransaction();
    limparFaturasDuplicadas($pdo);
    $pdo->commit();
} catch (Throwable $exception) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    sendJson([
        'success' => false,
        'message' => $exception->getMessage(),
    ], 422);
}

$stmt = $pdo->query(
    'SELECT f.id, f.paciente_id, p.nome AS paciente_nome, p.leito, p.plano_saude, f.valor_total, f.status, f.data_emissao, f.data_pagamento, f.usuario_id
     FROM faturas f
     INNER JOIN pacientes p ON p.id = f.paciente_id
     INNER JOIN (
        SELECT paciente_id, MAX(id) AS id
        FROM faturas
        GROUP BY paciente_id
     ) ultimas ON ultimas.id = f.id
     ORDER BY f.id DESC'
);

sendJson([
    'success' => true,
    'faturas' => $stmt->fetchAll(),
]);