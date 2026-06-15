<?php

require_once '../../config.php';

$data = getRequestData();

$pdo = getPdo();

$stmt = $pdo->prepare("
INSERT INTO pacientes
(nome, cpf, plano_saude, leito, data_internacao, status)
VALUES
(:nome, :cpf, :plano_saude, :leito, :data_internacao, :status)
");

$stmt->execute([
    'nome' => $data['nome'],
    'cpf' => $data['cpf'],
    'plano_saude' => $data['plano_saude'],
    'leito' => $data['leito'],
    'data_internacao' => $data['data_internacao'],
    'status' => $data['status']
]);

sendJson([
    'success' => true,
    'message' => 'Paciente cadastrado com sucesso'
]);