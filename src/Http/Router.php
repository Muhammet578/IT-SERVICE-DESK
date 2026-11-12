<?php

declare(strict_types=1);

namespace App\Http;

final class Router
{
    /** @var array<string, class-string> */
    private array $routes = [];

    /**
     * @param class-string $controllerClass
     */
    public function get(string $path, string $controllerClass): void
    {
        $this->routes['GET ' . $path] = $controllerClass;
    }

    public function dispatch(Request $request): Response
    {
        $routeKey = $request->method . ' ' . $request->path;
        $controllerClass = $this->routes[$routeKey] ?? null;

        if ($controllerClass === null) {
            return Response::html(
                '<h1>404 Not Found</h1><p>Запрошенная страница не найдена.</p>',
                404
            );
        }

        $controller = new $controllerClass();

        return $controller($request);
    }
}
