<?php

namespace App\Controller;

use App\Entity\Status;
use App\Entity\User;
use App\Form\TicketStatusType;
use App\Form\TicketEditType;
use App\Service\TicketCatalog;
use App\Security\Voter\TicketVoter;
use App\Repository\TicketRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



final class TicketController extends AbstractController
{
    #[Route('/tickets', name: 'app_tickets', methods: ['GET'])]
    public function index(
        Request $request,
        TicketRepository $ticketRepository,
        EntityManagerInterface $entityManager
    ): Response {
        // Получаем стандартный репозиторий статусов Doctrine.
        $statusRepository = $entityManager->getRepository(Status::class);

        $page = max(1, $request->query->getInt('page', 1));
        $limit = 15;

        $user = $this->getUser();

        if (!$user instanceof User || !$user->isActive()) {
            throw $this->createAccessDeniedException(
                'Доступ запрещён'
            );
        }

        // Получаем Symfony Session
        $session = $request->getSession();

        // Восстанавливаем последний выбранный статус
        $savedStatusId = $session->get('tickets_status_filter');

        $selectedStatus = is_int($savedStatusId)
            ? $statusRepository->find($savedStatusId)
            : null;

        // Пользователь выбрал новый фильтр
        if ($request->query->has('status')) {
            $requestedStatus = $request->query->get('status');

            $selectedStatus = null;

            // Разрешаем только положительный числовой ID
            if (
                is_string($requestedStatus)
                && ctype_digit($requestedStatus)
                && (int) $requestedStatus > 0
            ) {
                $selectedStatus = $statusRepository->find(
                    (int) $requestedStatus
                );
            }

            if ($selectedStatus === null) {
                // "Все статусы" или некорректный ID
                $session->remove('tickets_status_filter');
            } else {
                // Запоминаем выбранный статус
                $session->set(
                    'tickets_status_filter',
                    $selectedStatus->getId()
                );
            }
        }

        // Получаем заявки с учётом прав и фильтра
        $result = $ticketRepository->paginateForViewer(
            $user,
            $this->isGranted('ROLE_MANAGER'),
            $this->isGranted('ROLE_TECHNICIAN'),
            $page,
            $limit,
            $selectedStatus
        );

        $totalPages = max(
            1,
            (int) ceil($result['total'] / $limit)
        );

        if ($page > $totalPages) {
            return $this->redirectToRoute('app_tickets', [
                'page' => $totalPages,
                'status' => $selectedStatus?->getId() ?? 'all',
            ]);
        }

        return $this->render('ticket/index.html.twig', [
            'tickets' => $result['tickets'],
            'total' => $result['total'],
            'page' => $page,
            'totalPages' => $totalPages,
            'statuses' => $statusRepository->findBy(
                [],
                ['sortOrder' => 'ASC']
            ),
            'selectedStatusId' => $selectedStatus?->getId(),
        ]);
    }

    #[Route('/tickets/{id}', name: 'find_tickets', requirements: ['id' => '[1-9]\d*'], methods: ['GET'])]
    public function findById(
        int $id,
        TicketRepository $ticketRepository,
        TicketCatalog $catalog
    ): Response {
        // Получаем сущность Ticket через Doctrine
        $ticket = $ticketRepository->find($id);

        if ($ticket === null) {
            throw $this->createNotFoundException(
                'Заявка не найдена'
            );
        }

        // Проверяем права текущего пользователя
        $this->denyAccessUnlessGranted(
            TicketVoter::VIEW,
            $ticket
        );

        // Получаем данные для существующего Twig-шаблона
        $ticketData = $catalog->findById($id);

        return $this->render('ticket/show.html.twig', [
            'ticket' => $ticketData,
            'ticketEntity' => $ticket,
        ]);
    }

    #[Route('/tickets/{id}/edit', name: 'app_tickets_edit', requirements: ['id' => '[1-9]\d*'], methods: ['GET', 'POST'])]
    public function edit(
        int $id,
        Request $request,
        TicketRepository $ticketRepository,
        EntityManagerInterface $entityManager
    ): Response {
        // 1. Получаем заявку
        $ticket = $ticketRepository->find($id);

        if ($ticket === null) {
            throw $this->createNotFoundException(
                'Заявка не найдена'
            );
        }

        // 2. Проверяем право на редактирование
        $this->denyAccessUnlessGranted(
            TicketVoter::EDIT,
            $ticket
        );

        // 3. Создаём форму
        $form = $this->createForm(
            TicketEditType::class,
            $ticket
        );

        $form->handleRequest($request);

        // 4. Проверяем данные
        if ($form->isSubmitted() && $form->isValid()) {

            $ticket->setUpdatedAt(
                new \DateTimeImmutable()
            );

            // 5. Сохраняем изменения
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Заявка успешно обновлена!'
            );

            return $this->redirectToRoute(
                'find_tickets',
                ['id' => $ticket->getId()]
            );
        }


        return $this->render(
            'ticket/edit.html.twig',
            [
                'form' => $form->createView(),
                'ticket' => $ticket,
            ]
        );
    }
    #[Route(
        '/tickets/{id}/status',
        name: 'app_tickets_status',
        requirements: ['id' => '[1-9]\d*'],
        methods: ['GET', 'POST']
    )]
    public function changeStatus(
        int $id,
        Request $request,
        TicketRepository $ticketRepository,
        EntityManagerInterface $entityManager
    ): Response {
        // 1. Находим заявку
        $ticket = $ticketRepository->find($id);

        if ($ticket === null) {
            throw $this->createNotFoundException(
                'Заявка не найдена'
            );
        }

        // 2. Проверяем право изменения статуса
        $this->denyAccessUnlessGranted(
            TicketVoter::CHANGE_STATUS,
            $ticket
        );

        // 3. Создаём форму
        $form = $this->createForm(
            TicketStatusType::class,
            $ticket
        );

        $form->handleRequest($request);

        // 4. Проверяем отправку и корректность данных
        if ($form->isSubmitted() && $form->isValid()) {

            $ticket->setUpdatedAt(
                new \DateTimeImmutable()
            );

            // 5. Сохраняем новый статус
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Статус заявки успешно изменён!'
            );

            // 6. Возвращаемся к заявке
            return $this->redirectToRoute(
                'find_tickets',
                ['id' => $ticket->getId()]
            );
        }

        // 7. Отображаем форму
        return $this->render(
            'ticket/status.html.twig',
            [
                'form' => $form->createView(),
                'ticket' => $ticket,
            ]
        );
    }

    #[Route(
        '/tickets/{id}/delete',
        name: 'app_tickets_delete',
        requirements: ['id' => '[1-9]\d*'],
        methods: ['POST']
    )]
    public function delete(
        int $id,
        Request $request,
        TicketRepository $ticketRepository,
        EntityManagerInterface $entityManager
    ): Response {
        // Находим заявку
        $ticket = $ticketRepository->find($id);

        if ($ticket === null) {
            throw $this->createNotFoundException(
                'Заявка не найдена'
            );
        }

        // Проверяем права через TicketVoter
        $this->denyAccessUnlessGranted(
            TicketVoter::DELETE,
            $ticket
        );

        // Проверяем CSRF-токен
        if (!$this->isCsrfTokenValid(
            'delete_ticket_' . $id,
            $request->request->get('_token', '')
        )) {
            throw $this->createAccessDeniedException(
                'Недействительный CSRF-токен'
            );
        }

        // Удаляем заявку
        $entityManager->remove($ticket);
        $entityManager->flush();

        $this->addFlash(
            'success',
            'Заявка успешно удалена!'
        );

        return $this->redirectToRoute('app_tickets');
    }
}
