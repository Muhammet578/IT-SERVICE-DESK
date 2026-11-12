<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\Ticket;
use DateTimeImmutable;

final class TicketCatalog
{
    /**
     * @return list<Ticket>
     */
    public function all(): array
    {
        return [
            new Ticket(
                1001,
                'Не работает корпоративная почта',
                'Сотрудник не может отправлять письма из корпоративного почтового клиента.',
                'Новая',
                'Высокий',
                new DateTimeImmutable('2026-10-30T09:15:00')
            ),
            new Ticket(
                1002,
                'Настройка рабочего места',
                'Необходимо установить стандартный набор программ на новый компьютер.',
                'В работе',
                'Средний',
                new DateTimeImmutable('2026-10-30T10:40:00')
            ),
            new Ticket(
                1003,
                'Запрос доступа к общей папке',
                'Нужен доступ на чтение к папке отдела аналитики.',
                'Закрыта',
                'Низкий',
                new DateTimeImmutable('2026-10-29T16:20:00')
            ),
        ];
    }
}
