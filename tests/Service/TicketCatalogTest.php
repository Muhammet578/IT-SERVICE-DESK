<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Service\TicketCatalog;
use PHPUnit\Framework\TestCase;

final class TicketCatalogTest extends TestCase
{
    public function testReturnsDemoTickets(): void
    {
        $tickets = (new TicketCatalog())->all();

        self::assertCount(3, $tickets);
        self::assertSame(1001, $tickets[0]->id);
    }

    public function testEveryTicketHasExpectedDomainType(): void
    {
        $tickets = (new TicketCatalog())->all();

        foreach ($tickets as $ticket) {
            self::assertSame(\App\Domain\Ticket::class, $ticket::class);
        }
    }
}
