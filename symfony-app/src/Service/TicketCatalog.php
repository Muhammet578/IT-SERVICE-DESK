<?php

declare(strict_types=1);

namespace App\Service;

final class TicketCatalog
{
    public function findAll(): array
    {
        $tickets = [
            [
                'id' => 1,
                'title' => 'Не включается компьютер',
                'status' => 'Новая',
            ],
            [
                'id' => 2,
                'title' => 'Не работает интернет',
                'status' => 'В работе',
            ],
            [
                'id' => 3,
                'title' => 'Ошибка при входе',
                'status' => 'Закрыто',
            ]
        ];
        return $tickets;
    }

    public function findById(int $id): ?array
    {
        foreach ($this->findAll() as $ticket) {
            if ($ticket['id'] === $id) {
                return $ticket;
            }
        }
        return null;
    }
}
