<?php

declare(strict_types=1);

namespace App\Service;

use Doctrine\DBAL\Connection;

final class UserCatalog
{
    public function __construct(
        private Connection $connection
    ) {
        
    }

    public function findAll(): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT id, name, email FROM users ORDER BY id ASC'
        );
    }

    public function findById(int $id): ?array
    {
        $user = $this->connection->fetchAssociative(
            'SELECT id, name, email FROM users WHERE id = :id',
            ['id' => $id]
        );

        if($user === false){
            return null;
        }
        return $user;
    }
}