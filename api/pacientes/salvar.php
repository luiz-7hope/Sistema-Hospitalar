<?php

declare(strict_types=1);

require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJson(['success' => false, 'message' => 'Método não permitido.'], 405);
}

$data = getRequestData();

$nome = trim((string) ($data['nome'] ?? ''));
$cpf = trim((string) ($data['cpf'] ?? ''));
$planoSaude = trim((string) ($data['plano_saude'] ?? ''));
$leito = trim((string) ($data['leito'] ?? ''));
$dataInternacao = trim((string) ($data['data_internacao'] ?? ''));
$status = trim((string) ($data['status'] ?? 'Internado'));
$usuarioId = isset($data['usuario_id']) && $data['usuario_id'] !== '' ? (int) $data['usuario_id'] : null;

if ($nome === '' || $cpf === '' || $planoSaude === '' || $leito === '' || $dataInternacao === '') {
    sendJson(['success' => false, 'message' => 'Preencha todos os campos obrigatórios.'], 422);
}

$validStatuses = ['Internado', 'Alta Médica', 'Crítico'];
if (!in_array($status, $validStatuses, true)) {
    sendJson(['success' => false, 'message' => 'Status inválido.'], 422);
}

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dataInternacao)) {
    sendJson(['success' => false, 'message' => 'Data de internação inválida.'], 422);
}

$pdo = getPdo();

$check = $pdo->prepare('SELECT id FROM pacientes WHERE cpf = :cpf LIMIT 1');
$check->execute(['cpf' => $cpf]);

if ($check->fetch()) {
    sendJson(['success' => false, 'message' => 'Este CPF já está cadastrado.'], 409);
}

$insert = $pdo->prepare(
    'INSERT INTO pacientes (nome, cpf, plano_saude, leito, data_internacao, status, usuario_id) VALUES (:nome, :cpf, :plano_saude, :leito, :data_internacao, :status, :usuario_id)'
);
$insert->execute([
    'nome' => $nome,
    'cpf' => $cpf,
    'plano_saude' => $planoSaude,
    'leito' => $leito,
    'data_internacao' => $dataInternacao,
    'status' => $status,
    'usuario_id' => $usuarioId,
]);

$id = (int) $pdo->lastInsertId();
$stmt = $pdo->prepare('SELECT id, nome, cpf, plano_saude, leito, data_internacao, status, data_cadastro, usuario_id FROM pacientes WHERE id = :id LIMIT 1');
$stmt->execute(['id' => $id]);

sendJson([
    'success' => true,
    'message' => 'Paciente cadastrado com sucesso.',
    'paciente' => $stmt->fetch(),
]);