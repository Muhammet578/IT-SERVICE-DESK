<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class RequestInfoController extends AbstractController
{
    #[Route('/request-info', name: 'app_request_info', methods: ['GET'])]
    public function index(Request $request): JsonResponse
    {
        return $this->json([
            'method' => $request->getMethod(),
            'path' => $request->getPathInfo(),
            'query' => $request->query->all(),
            'userAgent' => $request->headers->get('User-Agent'),
        ]);
    }
}
