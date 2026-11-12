<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\Request;
use App\Http\Response;
use App\Service\TicketCatalog;

final class TicketController
{
    public function __construct(
        private readonly TicketCatalog $catalog = new TicketCatalog()
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $rows = '';

        foreach ($this->catalog->all() as $ticket) {
            $title = htmlspecialchars($ticket->title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $description = htmlspecialchars($ticket->description, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $status = htmlspecialchars($ticket->status, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $priority = htmlspecialchars($ticket->priority, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $createdAt = htmlspecialchars($ticket->createdAt->format('d.m.Y H:i'), ENT_QUOTES, 'UTF-8');

            $rows .= sprintf(
                '<tr><td>%d</td><td><strong>%s</strong><br><small>%s</small></td><td>%s</td><td>%s</td><td>%s</td></tr>',
                $ticket->id,
                $title,
                $description,
                $status,
                $priority,
                $createdAt
            );
        }

        return Response::html(<<<HTML
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>IT-заявки</title>
    <style>
        body{font:15px/1.5 Arial,sans-serif;background:#f3f5f8;color:#202733;margin:0;padding:32px}
        main{max-width:1050px;margin:auto;background:white;padding:28px;border-radius:12px}
        table{width:100%;border-collapse:collapse;margin-top:20px}
        th,td{text-align:left;padding:12px;border-bottom:1px solid #e5e8ed;vertical-align:top}
        th{background:#f7f8fa}small{color:#667085}a{color:#1769aa}
    </style>
</head>
<body><main>
    <h1>Примеры IT-заявок</h1>
    <p>Демонстрационные данные. Они не сохраняются в базе данных.</p>
    <table>
        <thead><tr><th>№</th><th>Заявка</th><th>Статус</th><th>Приоритет</th><th>Создана</th></tr></thead>
        <tbody>{$rows}</tbody>
    </table>
    <p><a href="/">На главную</a></p>
</main></body>
</html>
HTML);
    }
}
