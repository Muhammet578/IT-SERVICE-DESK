<?php

declare(strict_types=1);

namespace App\Http;

final class Router
{
    /** @var array<string, array{path: string, controller: class-string}> */
    private array $routes = [];

    /**
     * @param class-string $controllerClass
     */
    public function get(string $path, string $controllerClass): void
    {
        $this->addRoute('GET', $path, $controllerClass);
    }

    /**
     * @param class-string $controllerClass
     */
    public function post(string $path, string $controllerClass): void
    {
        $this->addRoute('POST', $path, $controllerClass);
    }

    /**
     * @param class-string $controllerClass
     */
    public function put(string $path, string $controllerClass): void
    {
        $this->addRoute('PUT', $path, $controllerClass);
    }

    /**
     * @param class-string $controllerClass
     */
    public function delete(string $path, string $controllerClass): void
    {
        $this->addRoute('DELETE', $path, $controllerClass);
    }

    /**
     * @param class-string $controllerClass
     */
    private function addRoute(
        string $method,
        string $path,
        string $controllerClass
    ): void {
        $this->routes[] = [
            'path' => $path,
            'controller' => $controllerClass,
            'method' => $method,
        ];
    }

    public function dispatch(Request $request): Response
    {
        foreach ($this->routes as $route) {
            if ($route['method'] !== $request->method) {
                continue;
            }

            $pattern = preg_replace(
                '/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/',
                '(?P<$1>[^/]+)',
                $route['path']
            );

            if (!is_string($pattern)) {
                continue;
            }

            $pattern = '#^' . $pattern . '$#';

            if (!preg_match($pattern, $request->path, $matches)) {
                continue;
            }

            $params = [];

            foreach ($matches as $key => $value) {
                if (is_string($key)) {
                    $params[$key] = $value;
                }
            }

            $matchedRequest = new Request(
                $request->method,
                $request->path,
                $request->query,
                $params,
                $request->body
            );

            $controllerClass = $route['controller'];
            $controller = new $controllerClass();

            return $controller($matchedRequest);
        }

        return Response::html(
            '<h1>404 Not Found</h1><p>Запрошенная страница не найдена.</p>',
            404
        );
    }
}