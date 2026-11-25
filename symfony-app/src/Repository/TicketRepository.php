<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Status;
use App\Entity\User;
use App\Entity\Ticket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;
/**
 * @extends ServiceEntityRepository<Ticket>
 */
final class TicketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ticket::class);
    }

    /**
     * @return Ticket[]
     */
    public function findByStatus(Status $status): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.status = :status')
            ->setParameter('status', $status)
            ->getQuery()
            ->getResult();
    }

    public function findByCreator(User $user): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.createdBy = :user')
            ->setParameter('user', $user)
            ->orderBy('t.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByPeriod(
        \DateTimeImmutable $from,
        \DateTimeImmutable $to
    ): array {
        return $this->createQueryBuilder('t')
            ->where('t.createdAt >= :from')
            ->andWhere('t.createdAt < :to')
            ->setParameter('from', $from, 'datetime_immutable')
            ->setParameter('to', $to, 'datetime_immutable')
            ->orderBy('t.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function paginate(int $page = 1, int $limit = 10): array
    {
        $page = max(1, $page);
        $limit = max(1, $limit);

        $query = $this->createQueryBuilder('t')
            ->leftJoin('t.status', 's')
            ->addSelect('s')
            ->orderBy('t.createdAt', 'DESC')
            ->addOrderBy('t.id', 'DESC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery();

        $paginator = new Paginator(
            $query,
            fetchJoinCollection: false
        );

        return [
            'tickets' => iterator_to_array(
                $paginator->getIterator(),
                false
            ),
            'total' => count($paginator),
        ];
    }
    public function paginateForViewer(
        User $user,
        bool $canViewAll,
        bool $canViewAssigned,
        int $page = 1,
        int $limit = 15,
        ?Status $status = null
    ): array {
        $page = max(1, $page);
        $limit = max(1, $limit);

        $qb = $this->createQueryBuilder('t')
            ->leftJoin('t.status', 's')
            ->addSelect('s')
            ->orderBy('t.createdAt', 'DESC')
            ->addOrderBy('t.id', 'DESC');

        // Менеджер и администратор видят все заявки
        if (!$canViewAll) {

            if ($canViewAssigned) {
                // Специалист видит свои и назначенные заявки
                $qb->andWhere(
                    $qb->expr()->orX(
                        't.createdBy = :viewer',
                        't.assignedTo = :viewer'
                    )
                );
            } else {
                // Обычный пользователь видит только свои
                $qb->andWhere('t.createdBy = :viewer');
            }

            $qb->setParameter('viewer', $user);
        }
        if ($status !== null) {
            $qb
                ->andWhere('t.status = :selectedStatus')
                ->setParameter('selectedStatus', $status);
        }

        $query = $qb
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery();

        $paginator = new Paginator(
            $query,
            fetchJoinCollection: false
        );

        return [
            'tickets' => iterator_to_array(
                $paginator->getIterator(),
                false
            ),
            'total' => count($paginator),
        ];
    }
}
