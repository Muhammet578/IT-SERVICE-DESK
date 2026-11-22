<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\UserFormData;
use Doctrine\DBAL\Connection;
use App\Model\UserEditData;

final class UserCatalog
{
    public function __construct(
        private Connection $connection
    ) {
        
    }
    public function update(int $id, UserEditData $data) : void 
    {
        $this->connection->update(
            'users',
            [
                'name' => $data->name,
                'email' => $data->email,
            ],
            [
                'id' => $id,
            ]
        );
    }

    public function create(UserFormData $data): int {
        $roleId = $this->connection->fetchOne(
            'SELECT id FROM roles WHERE name = :name',
            ['name' => 'user']
        );

        if($roleId === false){
            throw new \RuntimeException('Роль пользователя не найдена');   
        }

        $passwordHash = password_hash(
            $data->plainPassword,
            PASSWORD_DEFAULT
        );

        $this->connection->insert('users', [
            'role_id' => (int) $roleId,
            'name' => $data->name,
            'email' => $data->email,
            'password' => $passwordHash,
            'is_active' => 1
        ]);
        return (int) $this->connection->lastInsertId();
    }

    public function findAll(string $search = '', string $sort = 'id'): array
    {
        $allowedSort = ['id', 'name', 'email'];

        if (!in_array($sort, $allowedSort, true)) {
            $sort = 'id';
        }

        $sql = 'SELECT id, name, email FROM users';
        $params = [];

        if ($search !== '') {
            $sql .= ' WHERE name LIKE :name_search OR email LIKE :email_search';

            $params = [
                'name_search' => '%' . $search . '%',
                'email_search' => '%' . $search . '%',
            ];
        }

        $sql .= " ORDER BY {$sort} ASC";

        return $this->connection->fetchAllAssociative($sql, $params);
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