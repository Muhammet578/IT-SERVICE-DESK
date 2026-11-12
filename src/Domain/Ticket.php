<?php

declare(strict_types=1);

namespace App\Domain;

use DateTimeImmutable;
use InvalidArgumentException;

final readonly class Ticket
{
    public function __construct(
        public int $id,
        public string $title,
        public string $description,
        public string $status,
        public string $priority,
        public DateTimeImmutable $createdAt
    ) {
        if ($id <= 0) {
            throw new InvalidArgumentException('Идентификатор заявки должен быть положительным.');
        }

        if (trim($title) === '') {
            throw new InvalidArgumentException('Название заявки не может быть пустым.');
        }

        if (!in_array($status, ['Новая', 'В работе', 'Закрыта'], true)) {
            throw new InvalidArgumentException('Недопустимый статус заявки.');
        }

        if (!in_array($priority, ['Низкий', 'Средний', 'Высокий'], true)) {
            throw new InvalidArgumentException('Недопустимый приоритет заявки.');
        }
    }
}
