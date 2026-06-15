<?php

declare(strict_types=1);

require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJson(['success' => false, 'message' => 'Método não permitido.'], 405);
}

$data = getRequestData();

$email = trim((string) ($data['email'] ?? ''));
$senha = (string) ($data['senha'] ?? '');

if ($email === '' || $senha === '') {
    sendJson(['success' => false, 'message' => 'Informe email e senha.'], 422);
}

$pdo = getPdo();
$stmt = $pdo->prepare(
    'SELECT id, nome, email, senha_hash, cargo, hospital FROM usuarios WHERE email = :email AND ativo = 1 LIMIT 1'
);
$stmt->execute(['email' => $email]);
$usuario = $stmt->fetch();

if (!$usuario || !password_verify($senha, $usuario['senha_hash'])) {
    sendJson(['success' => false, 'message' => 'Email ou senha incorretos.'], 401);
}

sendJson([
    'success' => true,
    'message' => 'Login realizado com sucesso.',
    'user' => [
        'id' => (int) $usuario['id'],
        'nome' => $usuario['nome'],
        'email' => $usuario['email'],
        'cargo' => $usuario['cargo'],
        'hospital' => $usuario['hospital'],
    ],
]);
