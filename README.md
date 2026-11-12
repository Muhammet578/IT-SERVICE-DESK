# IT Service Desk — plain PHP

Учебное веб-приложение на PHP 8.4+ без фреймворка. Проект демонстрирует HTTP, Composer, PSR-4, ООП, Front Controller, обработку ошибок и PHPUnit.

> Важно: по условию ЛР №1 калькулятор `/quote` исключён. Вместо него добавлен учебный маршрут `/tickets`, соответствующий теме курсового проекта IT Service Desk. Это адаптация задания, а не реализация исходного калькулятора. Если преподаватель требует строго все маршруты из задания, `/quote` и `QuoteCalculator` придётся добавить отдельно.

## Требования

- PHP 8.4+
- Composer 2
- расширение `mbstring` и `xml` (обычно нужны PHPUnit)

Проверить версии:

```bash
php -v
composer --version
```

## Установка

Из каталога проекта:

```bash
composer install
composer dump-autoload
```

## Запуск

```bash
php -S localhost:8000 -t public
```

Откройте в браузере:

- <http://localhost:8000/>
- <http://localhost:8000/health>
- <http://localhost:8000/about>
- <http://localhost:8000/tickets>

## Проверка HTTP

```bash
curl -i http://localhost:8000/
curl -i http://localhost:8000/health
curl -i http://localhost:8000/about
curl -i http://localhost:8000/tickets
curl -i http://localhost:8000/unknown
curl -i -X POST http://localhost:8000/health
```

Ожидаемые результаты:
- `GET /`, `/about`, `/tickets` — `200 OK`;
- `GET /health` — `200 OK` и JSON;
- неизвестный маршрут — `404 Not Found`;
- метод, отличный от GET для зарегистрированного пути, — `404 Not Found` в этой минимальной реализации.

## Тесты

```bash
composer test
```

Либо:

```bash
vendor/bin/phpunit
```

## Структура

```text
plain-php/
├── composer.json
├── composer.lock                 # появится после composer install
├── phpunit.xml
├── README.md
├── public/
│   └── index.php                # единая точка входа (Front Controller)
├── src/
│   ├── Controller/
│   │   ├── AboutController.php
│   │   ├── HealthController.php
│   │   ├── HomeController.php
│   │   └── TicketController.php
│   ├── Domain/
│   │   └── Ticket.php
│   ├── Http/
│   │   ├── Request.php
│   │   ├── Response.php
│   │   └── Router.php
│   └── Service/
│       └── TicketCatalog.php
├── tests/
│   ├── Domain/TicketTest.php
│   └── Service/TicketCatalogTest.php
└── docs/
    └── ai/
        └── lab-01.md
```

## Примечание по архитектуре

- `public/index.php` получает HTTP-запрос, подключает автозагрузчик и запускает маршрутизацию.
- `Request` инкапсулирует данные входящего запроса.
- `Response` отвечает за HTTP-статус, заголовки и тело ответа.
- `Router` сопоставляет метод и путь с контроллером.
- Контроллеры формируют HTTP-ответы, но не хранят бизнес-логику.
- `Ticket` — доменная сущность заявки.
- `TicketCatalog` — сервис, предоставляющий демонстрационный набор заявок.

Данные заявок демонстрационные и хранятся в коде. База данных, регистрация пользователей, авторизация и создание заявок через форму в ЛР №1 не реализованы.

## Git

```bash
git init
git add .
git commit -m "feat: create plain PHP service desk application"
git tag lab-01
```

Проверить тег:

```bash
git tag
git show lab-01
```
