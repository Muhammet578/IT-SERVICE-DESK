<?php

namespace App\Controller;

use App\Service\TicketCatalog;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TicketController extends AbstractController
{
    #[Route('/tickets', name: 'app_tickets', methods: ['GET'])]
    public function index(TicketCatalog $catalog): Response
    {
        return $this->render('ticket/index.html.twig', [
            'tickets' => $catalog->findAll()
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
