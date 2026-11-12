<?php

declare(strict_types=1);

namespace App\Http;

final readonly class Request
{
    /**
     * @param array<string, mixed> $query
     */
    public function __construct(
        public string $method,
        public string $path,
        public array $query = []
    ) {
    }

    public static function fromGlobals(): self
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH);

        return new self(
            strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET'),
            is_string($path) && $path !== '' ? $path : '/',
            $_GET
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
}
