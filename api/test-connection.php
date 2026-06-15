<?php

require_once __DIR__ . '/config.php';

try {
    $pdo = getPdo();

    $versao = $pdo->query('SELECT VERSION()')->fetchColumn();
    $dbName = $pdo->query('SELECT DATABASE()')->fetchColumn();
    $one = $pdo->query('SELECT 1')->fetchColumn();

    sendJson([
        'success' => true,
        'message' => 'Conexão bem-sucedida.',
        'version' => $versao,
        'database' => $dbName,
        'test_select' => (int) $one
    ]);
} catch (Exception $e) {
    sendJson([
        'success' => false,
        'message' => 'Erro ao testar conexão.',
        'error' => $e->getMessage()
    ], 500);
}
