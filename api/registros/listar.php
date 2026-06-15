<?php

declare(strict_types=1);

require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendJson(['success' => false, 'message' => 'Método não permitido.'], 405);
}

$pdo = getPdo();

$stmt = $pdo->query(
    'SELECT r.id, r.paciente_id, p.nome AS paciente_nome, r.medicamento_id, m.nome AS medicamento_nome, r.quantidade, r.preco_total, r.data_uso, r.observacoes, r.data_cadastro, r.usuario_id
     FROM registros_uso r
     INNER JOIN pacientes p ON p.id = r.paciente_id
     INNER JOIN medicamentos m ON m.id = r.medicamento_id
     ORDER BY r.id DESC'
);

sendJson([
    'success' => true,
    'registros' => $stmt->fetchAll(),
]);