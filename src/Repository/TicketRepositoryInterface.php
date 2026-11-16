<?php

declare(strict_types=1);

namespace App\Repository;

interface TicketRepositoryInterface{
    
    public function findById(int $id): ?array;

    public function findAll(): array;

    public function create(array $data): int;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;

    public function findByUserId(int $userId): array;

    public function findByStatus(int $statusId): array;
}