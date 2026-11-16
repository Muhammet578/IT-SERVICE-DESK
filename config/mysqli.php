<?php

declare(strict_types=1);

$config = require __DIR__ . '/env.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host = $config['DB_HOST'] ?? '127.0.0.1';
$port = (int) ($config['DB_PORT'] ?? 3306);
$db_name = $config['DB_DATABASE'] ?? '';
$username = $config['DB_USERNAME'] ?? '';
$password = $config['DB_PASSWORD'] ?? '';

try {
    $mysqli = new mysqli(
        $host,
        $username,
        $password,
        $db_name,
        $port
    );

    $mysqli->set_charset('utf8mb4');

    return $mysqli;
} catch (mysqli_sql_exception $exception) {
    throw new RuntimeException(
        'Ошибка подключения к базе данных через MySQLi.',
        0,
        $exception
    );
}