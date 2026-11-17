<?php

declare(strict_types=1);

namespace App\Repository;

use mysqli;

class MysqliTicketRepository implements TicketRepositoryInterface
{
    public function __construct(
        private mysqli $mysqli
    ) {
    }

    // 1. Поиск заявки по ID
    public function findById(int $id): ?array
    {
        $sql = "SELECT * FROM tickets WHERE id = ?";

        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();

        $ticket = $stmt->get_result()->fetch_assoc();

        $stmt->close();

        return $ticket ?: null;
    }

    // 2. Получение всех заявок
    public function findAll(): array
    {
        $sql = "SELECT * FROM tickets";

        $result = $this->mysqli->query($sql);

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // 3. Создание заявки
    public function create(array $data): int
    {
        $sql = "
            INSERT INTO tickets (
                title,
                description,
                created_by,
                assigned_to,
                category_id,
                priority_id,
                status_id
            )
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ";

        $stmt = $this->mysqli->prepare($sql);

        $stmt->bind_param(
            'ssiiiii',
            $data['title'],
            $data['description'],
            $data['created_by'],
            $data['assigned_to'],
            $data['category_id'],
            $data['priority_id'],
            $data['status_id']
        );

        $stmt->execute();

        $id = $this->mysqli->insert_id;

        $stmt->close();

        return $id;
    }

    // 4. Обновление заявки
    public function update(int $id, array $data): bool
    {
        $sql = "
            UPDATE tickets
            SET title = ?, 
            description = ?,
            created_by   = ?,
            assigned_to  = ?,
            category_id  = ?,
            priority_id  = ?,
            status_id  = ?
            WHERE id = ?
        ";

        $stmt = $this->mysqli->prepare($sql);

        $stmt->bind_param(
            'ssi',
            $data['title'],
            $data['description'],
            $id
        );

        $stmt->execute();

        $updated = $stmt->affected_rows > 0;

        $stmt->close();

        return $updated;
    }

    // 5. Удаление заявки
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM tickets WHERE id = ?";

        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();

        $deleted = $stmt->affected_rows > 0;

        $stmt->close();

        return $deleted;
    }

    // 6. Поиск заявок пользователя
    public function findByUserId(int $userId): array
    {
        $sql = "SELECT * FROM tickets WHERE created_by = ?";

        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('i', $userId);
        $stmt->execute();

        $tickets = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $stmt->close();

        return $tickets;
    }

    // 7. Поиск заявок по статусу
    public function findByStatus(int $statusId): array
    {
        $sql = "SELECT * FROM tickets WHERE status_id = ?";

        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param('i', $statusId);
        $stmt->execute();

        $tickets = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $stmt->close();

        return $tickets;
    }
}