<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Ticket;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
final class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Получить всех активных пользователей.
     *
     * @return User[]
     */
    public function findActiveUsers(): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.isActive = :active')
            ->setParameter('active', true)
            ->orderBy('u.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Пять пользователей с наибольшим
     * количеством созданных заявок.
     *
     * @return array<int, array<string, mixed>>
     */
    public function findTopByTicketCount(int $limit = 5): array
    {
        return $this->createQueryBuilder('u')
            ->select(
                'u.id AS id',
                'u.name AS name',
                'COUNT(t.id) AS ticketsCount'
            )
            ->leftJoin(
                Ticket::class,
                't',
                'WITH',
                't.createdBy = u'
            )
            ->groupBy('u.id')
            ->addGroupBy('u.name')
            ->orderBy('ticketsCount', 'DESC')
            ->addOrderBy('u.id', 'ASC')
            ->setMaxResults(max(1, $limit))
            ->getQuery()
            ->getArrayResult();
    }
}
