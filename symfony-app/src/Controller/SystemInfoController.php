<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use DateTimeImmutable;

final class SystemInfoController extends AbstractController
{
    public function index(): JsonResponse
    {
        return $this->json([
            'application' => 'IT-SERVICE-DESK',
            'status' => 'ok',
            'php' => PHP_VERSION,
            'timestamp' => (new DateTimeImmutable())->format(DATE_ATOM),
        ]); 
    }
}
