<?php

declare(strict_types=1);

namespace App\Http;

final readonly class Response
{
    /**
     * @param array<string, string> $headers
     */
    public function __construct(
        private string $body,
        private int $statusCode = 200,
        private array $headers = ['Content-Type' => 'text/html; charset=UTF-8']
    ) {
    }

    public static function html(string $body, int $statusCode = 200): self
    {
        return new self($body, $statusCode);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function json(array $data, int $statusCode = 200): self
    {
        return new self(
            json_encode(
                $data,
                JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
            ),
            $statusCode,
            ['Content-Type' => 'application/json; charset=UTF-8']
        );
    }

    public function send(): void
    {
        http_response_code($this->statusCode);

        foreach ($this->headers as $name => $value) {
            header($name . ': ' . $value);
        }

        echo $this->body;
    }
}
