<?php

declare(strict_types=1);

class StatusSeeder
{
    public function run(PDO $pdo): void
    {
        $sql = "
            INSERT INTO statuses (name, is_final, sort_order)
            VALUES (:name, :is_final, :sort_order)
            ON DUPLICATE KEY UPDATE
                is_final = VALUES(is_final),
                sort_order = VALUES(sort_order)
        ";

        $stmt = $pdo->prepare($sql);

        $statuses = [
            [
                'name'       => 'Новая',
                'is_final'   => 0,
                'sort_order' => 1,
            ],
            [
                'name'       => 'В работе',
                'is_final'   => 0,
                'sort_order' => 2,
            ],
            [
                'name'       => 'Ожидает ответа',
                'is_final'   => 0,
                'sort_order' => 3,
            ],
            [
                'name'       => 'Решена',
                'is_final'   => 1,
                'sort_order' => 4,
            ],
            [
                'name'       => 'Закрыта',
                'is_final'   => 1,
                'sort_order' => 5,
            ],
        ];

        foreach ($statuses as $status) {
            $stmt->execute($status);
        }
    }
}