<?php

declare(strict_types=1);

namespace App\Service;

use Doctrine\DBAL\Connection;

final class TicketCatalog
{
    public function __construct(
        private Connection $connection
    ) {}

    public function findAll(): array
    {
        return $this->connection->fetchAllAssociative(
            '
        SELECT
            t.id,
            t.title,
            t.description,
            s.name AS status
        FROM tickets t
        JOIN statuses s ON t.status_id = s.id
        ORDER BY t.id ASC
        '
        );
    }

    public function findById(int $id): ?array
    {
        $ticket = $this->connection->fetchAssociative(
            '
        SELECT
            t.id,
            t.title,
            t.description,
            s.name AS status
        FROM tickets t
        JOIN statuses s ON t.status_id = s.id
        WHERE t.id = :id
        ',
        ['id' => $id]
        );

        if($ticket === false){
            return null;
        }
        return $ticket;
    }
}
