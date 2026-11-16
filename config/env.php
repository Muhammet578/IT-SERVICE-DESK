<?php

declare(strict_types=1);

$envPath = dirname(__DIR__) . '/.env';

if (!is_file($envPath)) {
    throw new RuntimeException('Файл .env не найден.');
}

$config = parse_ini_file($envPath, false, INI_SCANNER_RAW);

if ($config === false) {
    throw new RuntimeException('Не удалось прочитать файл .env.');
}

return $config;