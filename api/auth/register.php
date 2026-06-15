<?php

declare(strict_types=1);

require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJson(['success' => false, 'message' => 'Método não permitido.'], 405);
}

$data = getRequestData();

$nome = trim((string) ($data['nome'] ?? ''));
$email = trim((string) ($data['email'] ?? ''));
$cargo = trim((string) ($data['cargo'] ?? ''));
$senha = (string) ($data['senha'] ?? '');
$hospital = trim((string) ($data['hospital'] ?? ''));

if ($nome === '' || $email === '' || $cargo === '' || $senha === '' || $hospital === '') {
    sendJson(['success' => false, 'message' => 'Preencha todos os campos obrigatórios.'], 422);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    sendJson(['success' => false, 'message' => 'Email inválido.'], 422);
}

if (strlen($senha) < 6) {
    sendJson(['success' => false, 'message' => 'A senha deve ter no mínimo 6 caracteres.'], 422);
}

$pdo = getPdo();

$check = $pdo->prepare('SELECT id FROM usuarios WHERE email = :email LIMIT 1');
$check->execute(['email' => $email]);

if ($check->fetch()) {
    sendJson(['success' => false, 'message' => 'Este email já está cadastrado.'], 409);
}

$senhaHash = password_hash($senha, PASSWORD_DEFAULT);

$insert = $pdo->prepare(
    'INSERT INTO usuarios (nome, email, senha_hash, cargo, hospital) VALUES (:nome, :email, :senha_hash, :cargo, :hospital)'
);
$insert->execute([
    'nome' => $nome,
    'email' => $email,
    'senha_hash' => $senhaHash,
    'cargo' => $cargo,
    'hospital' => $hospital,
]);

sendJson([
    'success' => true,
    'message' => 'Cadastro realizado com sucesso.',
    'user' => [
        'id' => (int) $pdo->lastInsertId(),
        'nome' => $nome,
        'email' => $email,
        'cargo' => $cargo,
        'hospital' => $hospital,
    ],
]);
