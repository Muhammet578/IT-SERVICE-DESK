<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

class PdoTicketRepository implements TicketRepositoryInterface{
    public function __construct(
        private PDO $pdo
    ){
        }
        public function findById(int $id): ?array
        {
            $sql = "SELECT * FROM tickets WHERE id = :id";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute(['id' => $id]);

            $ticket = $stmt->fetch(PDO::FETCH_ASSOC);

            if($ticket === false){
                return null;
            }
            return $ticket;
        }

        
        public function findAll(): array
        {
         $sql = "SELECT * FROM tickets";

         $stmt = $this->pdo->prepare($sql);

         $stmt->execute();

         $ticket = $stmt->fetchAll(PDO::FETCH_ASSOC);

         return $ticket;
        }

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
                VALUES (
                    :title,
                    :description,
                    :created_by,
                    :assigned_to,
                    :category_id,
                    :priority_id,
                    :status_id
                )
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($data);

            return (int) $this->pdo->lastInsertId();
        }

        public function update(int $id, array $data): bool{
            $sql = "UPDATE tickets SET title = :title, 
            description = :description WHERE id = :id";

            $stmt = $this->pdo->prepare($sql);

            $params = [
                ...$data,
                'id' => $id
            ];

            $stmt->execute($params);

            return $stmt->rowCount() > 0;

        }

        public function delete(int $id): bool{
            $sql = "DELETE FROM tickets WHERE id = :id";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute(['id' => $id]);

            return $stmt->rowCount() > 0;
        }

        public function findByUserId(int $userId): array{
            $sql = "SELECT * FROM tickets WHERE created_by = :user_id";

            $stmt = $this->pdo->prepare($sql);

            $stmt->execute(['user_id' => $userId]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function findByStatus(int $statusId): array {
            $sql = "SELECT * FROM tickets WHERE status_id = :status_id";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['status_id' => $statusId]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
}
