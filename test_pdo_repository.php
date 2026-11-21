<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use App\Repository\PdoTicketRepository;

$pdo = require __DIR__ . '/config/database.php';

$repository = new PdoTicketRepository($pdo);

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

$failed = false;

try {
    // Начинаем транзакцию.
    $pdo->beginTransaction();

    echo "=== PDO Repository Tests ===" . PHP_EOL;

    // Получаем существующие связанные записи.
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

    // 1. CREATE
    $id = $repository->create([
        'title' => 'PDO test ticket',
        'description' => 'Testing PDO repository',
        'created_by' => $userId,
        'assigned_to' => $userId,
        'category_id' => $categoryId,
        'priority_id' => $priorityId,
        'status_id' => $statusId,
    ]);

    check($id > 0, 'create()');

    // 2. FIND BY ID
    $ticket = $repository->findById($id);

    check(
        $ticket !== null
        && (int) $ticket['id'] === $id,
        'findById()'
    );

    // 3. FIND ALL
    $tickets = $repository->findAll();

    check(
        is_array($tickets)
        && in_array(
            $id,
            array_map('intval', array_column($tickets, 'id')),
            true
        ),
        'findAll()'
    );

    // 4. FIND BY USER ID
    $userTickets = $repository->findByUserId($userId);

    check(
        in_array(
            $id,
            array_map('intval', array_column($userTickets, 'id')),
            true
        ),
        'findByUserId()'
    );

    // 5. FIND BY STATUS
    $statusTickets = $repository->findByStatus($statusId);

    check(
        in_array(
            $id,
            array_map('intval', array_column($statusTickets, 'id')),
            true
        ),
        'findByStatus()'
    );

    // 6. UPDATE
    $updated = $repository->update($id, [
        'title' => 'PDO updated ticket',
        'description' => 'Updated description',
        'created_by' => $userId,
        'assigned_to' => $userId,
        'category_id' => $categoryId,
        'priority_id' => $priorityId,
        'status_id' => $statusId,
    ]);

    check($updated, 'update()');

    $ticket = $repository->findById($id);

    check(
        $ticket !== null
        && $ticket['title'] === 'PDO updated ticket'
        && $ticket['description'] === 'Updated description',
        'update() changes database values'
    );

    // 7. DELETE
    $deleted = $repository->delete($id);

    check($deleted, 'delete()');

    // Проверяем, что заявка удалена.
    check(
        $repository->findById($id) === null,
        'Deleted ticket cannot be found'
    );

    echo PHP_EOL;
    echo "All PDO tests passed!" . PHP_EOL;

} catch (Throwable $exception) {
    $failed = true;

    echo PHP_EOL;
    echo "TEST ERROR: "
        . $exception->getMessage()
        . PHP_EOL;

} finally {
    // Отменяем тестовые изменения.
    if ($pdo->inTransaction()) {
        $pdo->rollBack();

        echo "Database changes rolled back." . PHP_EOL;
    }
}

if ($failed) {
    exit(1);
}