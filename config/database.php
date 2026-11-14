<?php

declare(strict_types=1);

$host = '127.0.0.1';
$port = 3306;
$db_name = 'it_service_desk';
$username = 'root';
$password = '';

$dsn = sprintf(
    'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
    $host,
    $port,
    $db_name
);

try{
    return new PDO(
        $dsn,
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
   
}catch (PDOException $exception){
    throw new RuntimeException(
        'Ошибка подключения к базе данных',
        previous: $exception
    );
}