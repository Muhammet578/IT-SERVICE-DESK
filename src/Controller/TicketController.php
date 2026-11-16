<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\Request;
use App\Http\Response;
use App\Repository\MysqliTicketRepository;
use App\Repository\PdoTicketRepository;
use App\Repository\TicketRepositoryInterface;
use InvalidArgumentException;

final class TicketController
{
    private TicketRepositoryInterface $repository;
    private string $driver;

    public function __construct()
    {
        $config = require dirname(__DIR__, 2) . '/config/env.php';

        $this->driver = strtolower($config['DB_DRIVER'] ?? 'pdo');

        if ($this->driver === 'pdo') {
            $pdo = require dirname(__DIR__, 2) . '/config/database.php';

            $this->repository = new PdoTicketRepository($pdo);

            return;
        }

        if ($this->driver === 'mysqli') {
            $mysqli = require dirname(__DIR__, 2) . '/config/mysqli.php';

            $this->repository = new MysqliTicketRepository($mysqli);

            return;
        }

        throw new InvalidArgumentException(
            'Неизвестный DB_DRIVER: ' . $this->driver
        );
    }

    public function __invoke(Request $request): Response
    {
        $tickets = $this->repository->findAll();

        $rows = '';

        foreach ($tickets as $ticket) {
            $id = (int) $ticket['id'];

            $title = htmlspecialchars(
                (string) $ticket['title'],
                ENT_QUOTES | ENT_SUBSTITUTE,
                'UTF-8'
            );

            $description = htmlspecialchars(
                (string) $ticket['description'],
                ENT_QUOTES | ENT_SUBSTITUTE,
                'UTF-8'
            );

            $statusId = (int) $ticket['status_id'];
            $priorityId = (int) $ticket['priority_id'];

            $createdAt = htmlspecialchars(
                (string) $ticket['created_at'],
                ENT_QUOTES | ENT_SUBSTITUTE,
                'UTF-8'
            );

            $rows .= sprintf(
                '<tr>
                    <td>%d</td>
                    <td><strong>%s</strong><br><small>%s</small></td>
                    <td>%d</td>
                    <td>%d</td>
                    <td>%s</td>
                </tr>',
                $id,
                $title,
                $description,
                $statusId,
                $priorityId,
                $createdAt
            );
        }

        return Response::html(<<<HTML
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>IT-заявки</title>
</head>
<body>

<h1>IT-заявки</h1>

<p>DB_DRIVER: <strong>{$this->driver}</strong></p>

<table border="1" cellpadding="10">
    <thead>
        <tr>
            <th>ID</th>
            <th>Заявка</th>
            <th>Status ID</th>
            <th>Priority ID</th>
            <th>Создана</th>
        </tr>
    </thead>

    <tbody>
        {$rows}
    </tbody>
</table>

</body>
</html>
HTML);
    }
}