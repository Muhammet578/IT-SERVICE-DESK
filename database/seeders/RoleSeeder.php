<?php

declare(strict_types=1);

class RoleSeeder {
    public function run(PDO $pdo): void {
        $sql = "
            INSERT INTO roles (name, description)
            VALUES (:name, :description)
            ON DUPLICATE KEY UPDATE
                description = VALUES(description)
        ";

        $stmt = $pdo->prepare($sql);

        $roles = [
            [
                'name' => 'Admin',
                'description' => 'System administrator',
            ],
            [
                'name' => 'Manager',
                'description' => 'Support manager',
            ],
            [
                'name' => 'User',
                'description' => 'User regular',
            ],
        ];

        foreach($roles as $role){
            $stmt->execute($role);
        };
    }
}