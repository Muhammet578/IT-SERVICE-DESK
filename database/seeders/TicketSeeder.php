<?php

declare(strict_types=1);

class TicketSeeder
{
    public function run(PDO $pdo): void
    {
        $count = $pdo->query("SELECT COUNT(*) FROM tickets")->fetchColumn();

        if ((int) $count > 0) {
        return;
        }
        
        $users = $pdo->query("
            SELECT email, id FROM users
        ")->fetchAll(PDO::FETCH_KEY_PAIR);

        $categories = $pdo->query("
            SELECT name, id FROM categories
        ")->fetchAll(PDO::FETCH_KEY_PAIR);

        $priorities = $pdo->query("
            SELECT name, id FROM priorities
        ")->fetchAll(PDO::FETCH_KEY_PAIR);

        $statuses = $pdo->query("
            SELECT name, id FROM statuses
        ")->fetchAll(PDO::FETCH_KEY_PAIR);

       
        if (
            !isset($users['stepan@exam.com'], $users['denis@exam.com'], $users['alena@exam.com'])
        ) {
            throw new RuntimeException('Не найдены необходимые пользователи');
        }

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

        $stmt = $pdo->prepare($sql);

        $tickets = [
            [
                'title' => 'Не включается компьютер',
                'description' => 'Компьютер не реагирует на кнопку включения.',
                'created_by' => 'stepan@exam.com',
                'assigned_to' => 'denis@exam.com',
                'category' => 'Оборудование',
                'priority' => 'Высокий',
                'status' => 'Новая',
            ],
            [
                'title' => 'Нет подключения к интернету',
                'description' => 'Отсутствует доступ к интернету на рабочем месте.',
                'created_by' => 'stepan@exam.com',
                'assigned_to' => 'denis@exam.com',
                'category' => 'Сеть и интернет',
                'priority' => 'Критический',
                'status' => 'В работе',
            ],
            [
                'title' => 'Не устанавливается программа',
                'description' => 'При установке программы появляется ошибка.',
                'created_by' => 'alena@exam.com',
                'assigned_to' => null,
                'category' => 'Программное обеспечение',
                'priority' => 'Средний',
                'status' => 'Новая',
            ],
            [
                'title' => 'Не работает корпоративная почта',
                'description' => 'Не удаётся отправлять и получать электронные письма.',
                'created_by' => 'alena@exam.com',
                'assigned_to' => 'denis@exam.com',
                'category' => 'Электронная почта',
                'priority' => 'Средний',
                'status' => 'Ожидает ответа',
            ],
            [
                'title' => 'Не удаётся войти в аккаунт',
                'description' => 'Система сообщает об ошибке авторизации.',
                'created_by' => 'stepan@exam.com',
                'assigned_to' => 'alena@exam.com',
                'category' => 'Учетные записи',
                'priority' => 'Высокий',
                'status' => 'Решена',
            ],
        ];

        foreach ($tickets as $ticket) {
            $stmt->execute([
                'title' => $ticket['title'],
                'description' => $ticket['description'],
                'created_by' => $users[$ticket['created_by']],
                'assigned_to' => $ticket['assigned_to'] !== null
                    ? $users[$ticket['assigned_to']]
                    : null,
                'category_id' => $categories[$ticket['category']],
                'priority_id' => $priorities[$ticket['priority']],
                'status_id' => $statuses[$ticket['status']],
            ]);
        }
    }
}