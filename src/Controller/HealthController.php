<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\Request;
use App\Http\Response;
use DateTimeImmutable;

final class HealthController
{
    public function __invoke(Request $request): Response
    {
        return Response::json([
            'status' => 'ok',
            'php' => PHP_VERSION,
            'timestamp' => (new DateTimeImmutable())->format(DATE_ATOM),
        ]);
    }
}
