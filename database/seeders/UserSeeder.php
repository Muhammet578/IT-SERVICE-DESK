<?php

declare(strict_types=1);

class UserSeeder{
    public function run(PDO $pdo): void {
        $sql = "
            INSERT INTO users (role_id, name, email, password)
            VALUES (:role_id, :name, :email, :password)
            ON DUPLICATE KEY UPDATE
                name = VALUES(name),
                role_id = VALUES(role_id)

        ";

        $stmt = $pdo->prepare($sql);

        $users = [
            [
                'role_id'   => 1,
                'name'      => 'Stepan',
                'email'     => 'stepan@exam.com',
                'password'  => password_hash('Stepan123!', PASSWORD_DEFAULT),
            ],
            [
                'role_id'   => 2,
                'name'      => 'Denis',
                'email'     => 'denis@exam.com',
                'password'  => password_hash('Denis123!', PASSWORD_DEFAULT),
            ],
            [
                'role_id'   => 3,
                'name'      => 'Alena',
                'email'     => 'alena@exam.com',
                'password'  => password_hash('Alena123!', PASSWORD_DEFAULT),
            ]
        ];

        foreach($users as $user){
            $stmt->execute($user);
        };
    }
}