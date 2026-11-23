<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Ticket;
use App\Repository\CategoryRepository;
use App\Repository\TicketRepository;
use App\Repository\UserRepository;
use Doctrine\DBAL\ParameterType;
use Doctrine\ORM\EntityManagerInterface;

final class DashboardService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository $userRepository,
        private readonly TicketRepository $ticketRepository,
        private readonly CategoryRepository $categoryRepository
    ) {}

    public function getStatistics(): array
    {
        // Общее количество пользователей
        $usersCount = $this->userRepository->count([]);

        // Общее количество заявок
        $ticketsCount = $this->ticketRepository->count([]);

        // DQL: количество незавершённых заявок
        $dql = '
            SELECT COUNT(t.id)
            FROM App\Entity\Ticket t
            JOIN t.status s
            WHERE s.isFinal = :isFinal
        ';

        $openTicketsCount = (int) $this->entityManager
            ->createQuery($dql)
            ->setParameter(
                'isFinal',
                false,
                ParameterType::BOOLEAN
            )
            ->getSingleScalarResult();

        // Завершённые заявки
        $closedTicketsCount =
            $ticketsCount - $openTicketsCount;

        // Среднее количество заявок на пользователя
        $averageTicketsPerUser = $usersCount > 0
            ? round($ticketsCount / $usersCount, 2)
            : 0.0;

        // Топ-5 пользователей по количеству заявок
        $topUsers = $this->userRepository
            ->findTopByTicketCount(5);

        // Топ-5 популярных категорий
        $topCategories = $this->categoryRepository
            ->findTopByTicketCount(5);

        return [
            'usersCount' => $usersCount,
            'ticketsCount' => $ticketsCount,
            'openTicketsCount' => $openTicketsCount,
            'closedTicketsCount' => $closedTicketsCount,
            'averageTicketsPerUser' => $averageTicketsPerUser,
            'topUsers' => $topUsers,
            'topCategories' => $topCategories,
        ];
    }
}
