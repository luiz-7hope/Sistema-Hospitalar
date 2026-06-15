<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

function sendJson(array $payload, int $statusCode = 200): void
{
    http_response_code($statusCode);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function getRequestData(): array
{
    $contentType = $_SERVER['CONTENT_TYPE'] ?? $_SERVER['HTTP_CONTENT_TYPE'] ?? '';

    if (stripos($contentType, 'application/json') !== false) {
        $body = file_get_contents('php://input');
        $data = json_decode($body ?: '', true);
        return is_array($data) ? $data : [];
    }

    return $_POST;
}

function getPdo(): PDO
{
    $host = '127.0.0.1';
    $port = '3306';
    $database = 'medicare_system';
    $username = 'root';
    $password = '';

    $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";

    try {
        return new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    } catch (PDOException $exception) {
        sendJson([
            'success' => false,
            'message' => 'Falha ao conectar ao banco de dados.',
            'error' => $exception->getMessage(),
        ], 500);
    }
}
