<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use App\Repository\MysqliTicketRepository;

$mysqli = require __DIR__ . '/config/mysqli.php';

$repository = new MysqliTicketRepository($mysqli);

/**
 * Проверка результата теста.
 */
function check(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException("FAILED: {$message}");
    }

    echo "[OK] {$message}" . PHP_EOL;
}

try {
    // Все изменения будут отменены после тестирования.
    $mysqli->begin_transaction();

    echo "=== MySQLi Repository Tests ===" . PHP_EOL;

    // Получаем существующие связанные записи.
    $user = $mysqli->query(
        "SELECT id FROM users LIMIT 1"
    )->fetch_assoc();

    $category = $mysqli->query(
        "SELECT id FROM categories LIMIT 1"
    )->fetch_assoc();

    $priority = $mysqli->query(
        "SELECT id FROM priorities LIMIT 1"
    )->fetch_assoc();

    $status = $mysqli->query(
        "SELECT id FROM statuses LIMIT 1"
    )->fetch_assoc();

    check(
        $user !== null
        && $category !== null
        && $priority !== null
        && $status !== null,
        'Required reference records exist'
    );

    // 1. CREATE
    $id = $repository->create([
        'title' => 'MySQLi test ticket',
        'description' => 'Testing repository methods',
        'created_by' => (int) $user['id'],
        'assigned_to' => (int) $user['id'],
        'category_id' => (int) $category['id'],
        'priority_id' => (int) $priority['id'],
        'status_id' => (int) $status['id'],
    ]);

    check($id > 0, 'create()');

    // 2. FIND BY ID
    $ticket = $repository->findById($id);

    check(
        $ticket !== null && (int) $ticket['id'] === $id,
        'findById()'
    );

    // 3. FIND ALL
    $tickets = $repository->findAll();

    check(
        is_array($tickets)
        && in_array($id, array_map('intval', array_column($tickets, 'id')), true),
        'findAll()'
    );

    // 4. FIND BY USER ID
    $userTickets = $repository->findByUserId(
        (int) $user['id']
    );

    check(
        in_array($id, array_map('intval', array_column($userTickets, 'id')), true),
        'findByUserId()'
    );

    // 5. FIND BY STATUS
    $statusTickets = $repository->findByStatus(
        (int) $status['id']
    );

    check(
        in_array($id, array_map('intval', array_column($statusTickets, 'id')), true),
        'findByStatus()'
    );

    // 6. UPDATE
    $updated = $repository->update($id, [
        'title' => 'MySQLi updated ticket',
        'description' => 'Updated description',
        'created_by' => (int) $user['id'],
        'assigned_to' => (int) $user['id'],
        'category_id' => (int) $category['id'],
        'priority_id' => (int) $priority['id'],
        'status_id' => (int) $status['id'],
    ]);

    check($updated, 'update()');

    $ticket = $repository->findById($id);

    check(
        $ticket !== null
        && $ticket['title'] === 'MySQLi updated ticket'
        && $ticket['description'] === 'Updated description',
        'update() changes database values'
    );

    // 7. DELETE
    $deleted = $repository->delete($id);

    check($deleted, 'delete()');

    check(
        $repository->findById($id) === null,
        'Deleted ticket cannot be found'
    );

    echo PHP_EOL . "All MySQLi tests passed!" . PHP_EOL;

} catch (Throwable $exception) {
    echo PHP_EOL . "TEST ERROR: "
        . $exception->getMessage()
        . PHP_EOL;

    exit(1);

} finally {
    // Отменяем изменения даже при ошибке тестирования.
    try {
        $mysqli->rollback();
        echo "Database changes rolled back." . PHP_EOL;
    } finally {
        $mysqli->close();
    }
}