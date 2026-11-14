<?php

declare(strict_types=1);

class PrioritySeeder
{
    public function run(PDO $pdo): void
    {
        $sql = "
            INSERT INTO priorities (name, level)
            VALUES (:name, :level)
            ON DUPLICATE KEY UPDATE
                level = VALUES(level)
        ";

        $stmt = $pdo->prepare($sql);

        $priorities = [
            [
                'name'  => 'Низкий',
                'level' => 1,
            ],
            [
                'name'  => 'Средний',
                'level' => 2,
            ],
            [
                'name'  => 'Высокий',
                'level' => 3,
            ],
            [
                'name'  => 'Критический',
                'level' => 4,
            ],
        ];

        foreach ($priorities as $priority) {
            $stmt->execute($priority);
        }
    }
}