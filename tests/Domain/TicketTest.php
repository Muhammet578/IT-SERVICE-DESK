<?php

declare(strict_types=1);

namespace App\Tests\Domain;

use App\Domain\Ticket;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class TicketTest extends TestCase
{
    public function testCreatesValidTicket(): void
    {
        $ticket = new Ticket(
            1,
            'Не работает почта',
            'Описание проблемы',
            'Новая',
            'Высокий',
            new DateTimeImmutable('2026-10-30T10:00:00')
        );

        self::assertSame(1, $ticket->id);
        self::assertSame('Новая', $ticket->status);
    }

    public function testRejectsNonPositiveId(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Ticket(
            0,
            'Заявка',
            'Описание',
            'Новая',
            'Средний',
            new DateTimeImmutable()
        );
    }

    public function testRejectsEmptyTitle(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Ticket(
            1,
            '  ',
            'Описание',
            'Новая',
            'Средний',
            new DateTimeImmutable()
        );
    }

    public function testRejectsUnknownStatus(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Ticket(
            1,
            'Заявка',
            'Описание',
            'Ожидает',
            'Средний',
            new DateTimeImmutable()
        );
    }

    public function testRejectsUnknownPriority(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Ticket(
            1,
            'Заявка',
            'Описание',
            'Новая',
            'Критический',
            new DateTimeImmutable()
        );
    }
}
