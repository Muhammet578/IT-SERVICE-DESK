<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use App\Repository\PdoTicketRepository;
use App\Repository\MysqliTicketRepository;
use App\Repository\TicketRepositoryInterface;

/**
 * Проверяет результат теста.
 */
function check(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException("FAILED: {$message}");
    }

    echo "[OK] {$message}" . PHP_EOL;
}

/**
 * Сохраняет исходные данные заявок для сравнения.
 */
function getTicketSnapshot(
    TicketRepositoryInterface $repository
): array {
    $result = [];

    foreach ($repository->findAll() as $ticket) {
        $result[(int) $ticket['id']] = [
            'title' => $ticket['title'],
            'description' => $ticket['description'],
        ];
    }

    ksort($result);

    return $result;
}

/**
 * Проверка защиты от SQL Injection.
 */
function runSecurityTest(
    TicketRepositoryInterface $repository,
    array $data
): void {
    $injection = "' OR 1=1 -- ";

    // Запоминаем существующие заявки.
    $before = getTicketSnapshot($repository);

    // 1. Проверяем CREATE.
    $data['title'] = $injection;
    $data['description'] = 'SQL Injection test';

    $id = $repository->create($data);

    check($id > 0, 'create() with SQL Injection');

    // 2. Проверяем сохранённое значение.
    $ticket = $repository->findById($id);

    check(
        $ticket !== null
        && $ticket['title'] === $injection,
        'SQL Injection stored as plain text'
    );

    // 3. Проверяем UPDATE.
    $data['title'] = 'Updated security test';
    $data['description'] = $injection;

    $updated = $repository->update($id, $data);

    check($updated, 'update() with SQL Injection');

    $ticket = $repository->findById($id);

    check(
        $ticket !== null
        && $ticket['description'] === $injection,
        'SQL Injection did not execute during UPDATE'
    );

    // 4. Убеждаемся, что другие заявки не изменились.
    $after = getTicketSnapshot($repository);

    unset($after[$id]);

    check(
        $before === $after,
        'Other tickets remain unchanged'
    );
}

try {
    echo "=== SQL Injection Security Tests ===" . PHP_EOL;

    // Получаем PDO-подключение.
    $pdo = require __DIR__ . '/config/database.php';

    $userId = (int) $pdo->query(
        "SELECT id FROM users LIMIT 1"
    )->fetchColumn();

    $categoryId = (int) $pdo->query(
        "SELECT id FROM categories LIMIT 1"
    )->fetchColumn();

    $priorityId = (int) $pdo->query(
        "SELECT id FROM priorities LIMIT 1"
    )->fetchColumn();

    $statusId = (int) $pdo->query(
        "SELECT id FROM statuses LIMIT 1"
    )->fetchColumn();

    check(
        $userId > 0
        && $categoryId > 0
        && $priorityId > 0
        && $statusId > 0,
        'Required reference records exist'
    );

    $data = [
        'created_by' => $userId,
        'assigned_to' => $userId,
        'category_id' => $categoryId,
        'priority_id' => $priorityId,
        'status_id' => $statusId,
    ];

    // Проверка PDO.
    echo PHP_EOL . "--- PDO ---" . PHP_EOL;

    $pdo->beginTransaction();

    try {
        $pdoRepository = new PdoTicketRepository($pdo);

        runSecurityTest($pdoRepository, $data);
    } finally {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
            echo "PDO: Database changes rolled back." . PHP_EOL;
        }
    }

    // Проверка MySQLi.
    echo PHP_EOL . "--- MySQLi ---" . PHP_EOL;

    $mysqli = require __DIR__ . '/config/mysqli.php';

    try {
        $mysqli->begin_transaction();

        try {
            $mysqliRepository = new MysqliTicketRepository($mysqli);

            runSecurityTest($mysqliRepository, $data);
        } finally {
            $mysqli->rollback();
            echo "MySQLi: Database changes rolled back." . PHP_EOL;
        }
    } finally {
        $mysqli->close();
    }

    echo PHP_EOL;
    echo "All SQL Injection tests passed!" . PHP_EOL;

} catch (Throwable $exception) {
    echo PHP_EOL;
    echo "TEST ERROR: " . $exception->getMessage() . PHP_EOL;
    exit(1);
}