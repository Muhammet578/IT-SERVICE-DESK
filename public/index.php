<?php

declare(strict_types=1);

use App\Controller\AboutController;
use App\Controller\HealthController;
use App\Controller\HomeController;
use App\Controller\TicketController;
use App\Http\Request;
use App\Http\Response;
use App\Http\Router;

require dirname(__DIR__) . '/vendor/autoload.php';

$request = Request::fromGlobals();
$router = new Router();

$router->get('/', HomeController::class);
$router->get('/health', HealthController::class);
$router->get('/about', AboutController::class);
$router->get('/tickets', TicketController::class);

try {
    $response = $router->dispatch($request);
} catch (InvalidArgumentException $exception) {
    $message = htmlspecialchars(
        $exception->getMessage(),
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );

    $response = Response::html(
        '<h1>400 Bad Request</h1><p>' . $message . '</p>',
        400
    );
} catch (Throwable $exception) {
    error_log((string) $exception);

    $response = Response::html(
        '<h1>500 Internal Server Error</h1><p>Внутренняя ошибка сервера.</p>',
        500
    );
}

$response->send();
