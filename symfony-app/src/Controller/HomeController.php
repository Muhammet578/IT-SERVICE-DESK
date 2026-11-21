<?php
declare(strict_types=1);

namespace App\Controller;

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

    #[Route('/dashboard', name: 'app_dashboard', methods: ['GET'])]
    public function dashboard(): Response 
    {
        
        $ticketsCount = 10;
        return $this->render('home/dashboard.html.twig', [
            'project_name' => 'IT_SERVICE_DESK',
            'tickets_count' => $ticketsCount,
        ]);
    }
}
