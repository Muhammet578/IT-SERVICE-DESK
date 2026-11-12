<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\Request;
use App\Http\Response;

final class HomeController
{
    public function __invoke(Request $request): Response
    {
        return Response::html(<<<'HTML'
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>IT Service Desk — главная</title>
    <style>
        *{box-sizing:border-box}
        body{font:16px/1.6 Arial,sans-serif;background:#f3f5f8;color:#202733;margin:0}
        main{max-width:850px;margin:56px auto;padding:36px;background:#fff;border-radius:14px}
        h1{margin:12px 0}a{color:#1769aa}.tag{display:inline-block;background:#e8f2fb;padding:5px 10px;border-radius:20px;color:#14598d}
        .links{display:flex;gap:18px;flex-wrap:wrap;margin-top:24px}
    </style>
</head>
<body><main>
    <span class="tag">Учебный проект PHP 8.4+</span>
    <h1>IT Service Desk</h1>
    <p>Демонстрационное веб-приложение для регистрации и обработки обращений сотрудников в IT-службу.</p>
    <h2>Возможности демонстрационной версии</h2>
    <ul>
        <li>просмотр примеров IT-заявок;</li>
        <li>отображение статусов и приоритетов;</li>
        <li>проверка доступности приложения через health endpoint.</li>
    </ul>
    <nav class="links">
        <a href="/about">О проекте</a>
        <a href="/tickets">Примеры заявок</a>
        <a href="/health">Состояние приложения (JSON)</a>
    </nav>
</main></body>
</html>
HTML);
    }
}
