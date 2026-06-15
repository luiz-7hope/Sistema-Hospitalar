<?php

declare(strict_types=1);

require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJson(['success' => false, 'message' => 'Método não permitido.'], 405);
}

$data = getRequestData();

$nome = trim((string) ($data['nome'] ?? ''));
$codigo = trim((string) ($data['codigo'] ?? ''));
$categoria = trim((string) ($data['categoria'] ?? ''));
$precoUnitario = $data['preco_unitario'] ?? null;
$estoque = $data['estoque'] ?? null;
$usuarioId = isset($data['usuario_id']) && $data['usuario_id'] !== '' ? (int) $data['usuario_id'] : null;

if ($nome === '' || $codigo === '' || $categoria === '' || $precoUnitario === null || $estoque === null) {
    sendJson(['success' => false, 'message' => 'Preencha todos os campos obrigatórios.'], 422);
}

$precoUnitario = (float) $precoUnitario;
$estoque = (int) $estoque;

if ($precoUnitario <= 0 || $estoque < 0) {
    sendJson(['success' => false, 'message' => 'Preço e estoque inválidos.'], 422);
}

$pdo = getPdo();

$check = $pdo->prepare('SELECT id FROM medicamentos WHERE codigo = :codigo LIMIT 1');
$check->execute(['codigo' => $codigo]);

if ($check->fetch()) {
    sendJson(['success' => false, 'message' => 'Este código já está cadastrado.'], 409);
}

$insert = $pdo->prepare(
    'INSERT INTO medicamentos (nome, codigo, categoria, preco_unitario, estoque, usuario_id) VALUES (:nome, :codigo, :categoria, :preco_unitario, :estoque, :usuario_id)'
);
$insert->execute([
    'nome' => $nome,
    'codigo' => $codigo,
    'categoria' => $categoria,
    'preco_unitario' => $precoUnitario,
    'estoque' => $estoque,
    'usuario_id' => $usuarioId,
]);

$id = (int) $pdo->lastInsertId();
$stmt = $pdo->prepare('SELECT id, nome, codigo, categoria, preco_unitario, estoque, data_cadastro, usuario_id FROM medicamentos WHERE id = :id LIMIT 1');
$stmt->execute(['id' => $id]);

sendJson([
    'success' => true,
    'message' => 'Medicamento cadastrado com sucesso.',
    'medicamento' => $stmt->fetch(),
]);