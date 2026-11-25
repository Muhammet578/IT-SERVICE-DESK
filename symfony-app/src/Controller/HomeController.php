<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\DashboardService;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
        Request $request,
        DashboardService $dashboardService
    ): Response {
        $session = $request->getSession();

        $allowedPeriods = ['7', '30', 'all'];

        // Восстанавливаем предыдущий выбор из Session.
        $period = (string) $session->get('dashboard_period', '30');

        if (!in_array($period, $allowedPeriods, true)) {
            $period = '30';
        }

        // Если пользователь выбрал новый период,
        // проверяем значение и сохраняем его.
        $requestedPeriod = $request->query->get('period');

        if (in_array($requestedPeriod, $allowedPeriods, true)) {
            $period = $requestedPeriod;
            $session->set('dashboard_period', $period);
        }

        // Для all дата начала не нужна.
        $since = $period === 'all'
            ? null
            : (new DateTimeImmutable())->modify(
                sprintf('-%d days', (int) $period)
            );

        $statistics = $dashboardService->getStatistics($since);

        return $this->render('home/dashboard.html.twig', [
            'project_name' => 'IT_SERVICE_DESK',
            'tickets_count' => $statistics['ticketsCount'],
            'stats' => $statistics,
            'selectedPeriod' => $period,
        ]);
    }
}
