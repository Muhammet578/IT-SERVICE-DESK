<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Ticket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 */
final class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    /**
     * Пять категорий с наибольшим количеством заявок.
     *
     * @return array<int, array<string, mixed>>
     */
    public function findTopByTicketCount(int $limit = 5): array
    {
        return $this->createQueryBuilder('c')
            ->select(
                'c.id AS id',
                'c.name AS name',
                'COUNT(t.id) AS ticketsCount'
            )
            ->leftJoin(
                Ticket::class,
                't',
                'WITH',
                't.category = c'
            )
            ->groupBy('c.id')
            ->addGroupBy('c.name')
            ->orderBy('ticketsCount', 'DESC')
            ->addOrderBy('c.id', 'ASC')
            ->setMaxResults(max(1, $limit))
            ->getQuery()
            ->getArrayResult();
    }
}
