<?php

declare(strict_types=1);

use App\Repository\MysqliTicketRepository;
use App\Repository\PdoTicketRepository;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$config = require __DIR__ . '/env.php';

$driver = strtolower(trim($config['DB_DRIVER'] ?? 'pdo'));

return match ($driver) {
    'pdo' => new PdoTicketRepository(
        require __DIR__ . '/database.php'
    ),

    'mysqli' => new MysqliTicketRepository(
        require __DIR__ . '/mysqli.php'
    ),

    default => throw new InvalidArgumentException(
        "Неизвестный драйвер БД: {$driver}"
    ),
};