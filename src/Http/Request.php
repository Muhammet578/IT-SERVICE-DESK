<?php

declare(strict_types=1);

namespace App\Http;

final readonly class Request
{
    /**
     * @param array<string, mixed> $query
     * @param array<string, string> $params
     */
    public function __construct(
        public string $method,
        public string $path,
        public array $query = [],
        public array $params = [],
        public array $body = []
    ) {
    }

    public static function fromGlobals(): self
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH);
        $body = $_POST;

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'PUT'){
            $rawBody = file_get_contents('php://input');

            parse_str($rawBody, $body);
            
        };

        return new self(
            strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET'),
            is_string($path) && $path !== '' ? $path : '/',
            $_GET,
            [],
            $body
        );
    }

    public function queryString(string $key, string $default = ''): string
    {
        $value = $this->query[$key] ?? $default;

        if (!is_scalar($value)) {
            throw new \InvalidArgumentException(
                sprintf('Параметр "%s" должен быть простым значением.', $key)
            );
        }

        return (string) $value;
    }

    public function param(string $key): string
    {
        if (!isset($this->params[$key])) {
            throw new \InvalidArgumentException(
                sprintf('Параметр маршрута "%s" не найден.', $key)
            );
        }

        return $this->params[$key];
    }
}