<?php

declare(strict_types=1);

namespace App\Service;

final class UserCatalog
{
    public function findAll(): array
    {
        $users = [
            [
                'id' => 1,
                'name' => 'Алексей',
                'email' => 'alex@example.com',
            ],
            [
                'id' => 2,
                'name' => 'Володя',
                'email' => 'alex@example.com',
            ],
            [
                'id' => 3,
                'name' => 'Митя',
                'email' => 'alex@example.com',
            ]
        ];
        return $users;
    }

    public function findById(int $id): ?array
    {
        foreach($this->findAll() as $user){
            if($user['id'] === $id){
                return $user;
            }
        }
        return null;
    }
}