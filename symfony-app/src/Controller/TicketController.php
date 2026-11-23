<?php

namespace App\Controller;

use App\Service\TicketCatalog;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\TicketRepository;
use Symfony\Component\HttpFoundation\Request;

final class TicketController extends AbstractController
{
    #[Route('/tickets', name: 'app_tickets', methods: ['GET'])]
    public function index(
        Request $request,
        TicketRepository $ticketRepository
    ): Response {
        $page = max(1, $request->query->getInt('page', 1));
        $limit = 15;

        $result = $ticketRepository->paginate($page, $limit);

        $totalPages = max(
            1,
            (int) ceil($result['total'] / $limit)
        );

        if ($page > $totalPages) {
            return $this->redirectToRoute('app_tickets', [
                'page' => $totalPages,
            ]);
        }

        return $this->render('ticket/index.html.twig', [
            'tickets' => $result['tickets'],
            'total' => $result['total'],
            'page' => $page,
            'totalPages' => $totalPages,
        ]);
    }

    #[Route('/tickets/{id}', name: 'find_tickets', requirements: ['id' => '[1-9]\d*'], methods: ['GET'])]
    public function findById(int $id, TicketCatalog $catalog): Response
    {
        $ticket = $catalog->findById($id);
        if($ticket === null){
            throw $this->createNotFoundException('Заявка не найдена');
        }
        return $this->render('ticket/show.html.twig', ['ticket' => $ticket]);
    }
}
