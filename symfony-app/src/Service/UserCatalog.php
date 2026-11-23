<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\UserFormData;
use Doctrine\DBAL\Connection;
use App\Model\UserEditData;
use App\Entity\User;
use App\Entity\Role;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserCatalog
{
    public function __construct(
        private Connection $connection,
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {}
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

    public function create(UserFormData $data): int
    {
        $role = $this->entityManager
            ->getRepository(Role::class)
            ->findOneBy(['name' => 'user']);

        if ($role === null) {
            throw new \RuntimeException('Роль user не найдена');
        }

        $user = new User();

        $user->setName($data->name);
        $user->setEmail($data->email);
        $user->setRole($role);
        $user->setIsActive(true);

        $passwordHash = $this->passwordHasher->hashPassword(
            $user,
            $data->plainPassword
        );

        $user->setPasswordHash($passwordHash);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user->getId();
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