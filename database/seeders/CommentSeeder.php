<?php

declare(strict_types=1);

class CommentSeeder
{
    public function run(PDO $pdo): void
    {
        $users = $pdo->query("
            SELECT email, id FROM users
        ")->fetchAll(PDO::FETCH_KEY_PAIR);


        $tickets = $pdo->query("
            SELECT title, id FROM tickets
        ")->fetchAll(PDO::FETCH_KEY_PAIR);

        $sql = "
            INSERT INTO comments (
                ticket_id,
                user_id,
                content
            )
            VALUES (
                :ticket_id,
                :user_id,
                :content
            )
        ";

        $stmt = $pdo->prepare($sql);

        $comments = [
            [
                'ticket' => 'Не включается компьютер',
                'user' => 'denis@exam.com',
                'content' => 'Проверяю блок питания компьютера.',
            ],
            [
                'ticket' => 'Нет подключения к интернету',
                'user' => 'denis@exam.com',
                'content' => 'Проверяю сетевое подключение.',
            ],
            [
                'ticket' => 'Не устанавливается программа',
                'user' => 'alena@exam.com',
                'content' => 'Уточните версию программы.',
            ],
            [
                'ticket' => 'Не работает корпоративная почта',
                'user' => 'denis@exam.com',
                'content' => 'Проверяю настройки почтового сервера.',
            ],
            [
                'ticket' => 'Не удаётся войти в аккаунт',
                'user' => 'alena@exam.com',
                'content' => 'Доступ восстановлен.',
            ],
        ];

        foreach ($comments as $comment) {
            if (
                !isset($tickets[$comment['ticket']]) ||
                !isset($users[$comment['user']])
            ) {
                throw new RuntimeException(
                    'Не найдена заявка или пользователь для комментария'
                );
            }

            $stmt->execute([
                'ticket_id' => $tickets[$comment['ticket']],
                'user_id' => $users[$comment['user']],
                'content' => $comment['content'],
            ]);
        }
    }
}