<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\DashboardService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'project_name' => 'IT_SERVICE_DESK',
        ]);
    }

    public function about(): Response
    {
        return $this->render('home/about.html.twig', [
            'project_name' => 'IT_SERVICE_DESK',
        ]);
    }

    #[Route('/dashboard', name: 'app_dashboard', methods: ['GET'])]
    public function dashboard(
        DashboardService $dashboardService
    ): Response {
        $statistics = $dashboardService->getStatistics();

        return $this->render('home/dashboard.html.twig', [
            'project_name' => 'IT_SERVICE_DESK',
            'tickets_count' => $statistics['ticketsCount'],
            'stats' => $statistics,
        ]);
    }
}
