<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\Request;
use App\Http\Response;

final class AboutController
{
    public function __invoke(Request $request): Response
    {
        return Response::html(<<<'HTML'
<!doctype html>
<html lang="ru">
<head><meta charset="UTF-8"><title>О проекте</title></head>
<body>
    <h1>О проекте IT Service Desk</h1>
    <p>Это учебный проект для освоения PHP, HTTP, Composer, PSR-4 и объектно-ориентированного программирования.</p>
    <p>В дальнейшем концепция может быть расширена до корпоративной системы управления IT-заявками.</p>
    <a href="/">На главную</a>
</body>
</html>
HTML);
    }
}
