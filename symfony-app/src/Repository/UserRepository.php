<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Ticket;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

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

    public function paginate(
        int $page = 1,
        int $limit = 10,
        string $search = '',
        string $sort = 'id'
    ): array {
        $page = max(1, $page);
        $limit = max(1, $limit);

        // Разрешаем сортировку только по этим полям
        $allowedSort = ['id', 'name', 'email'];

        if (!in_array($sort, $allowedSort, true)) {
            $sort = 'id';
        }

        $qb = $this->createQueryBuilder('u');

        // Поиск по имени и email
        if ($search !== '') {
            $qb->where(
                'u.name LIKE :search OR u.email LIKE :search'
            )->setParameter('search', '%' . $search . '%');
        }

        // Сортировка
        $qb->orderBy('u.' . $sort, 'ASC');

        if ($sort !== 'id') {
            $qb->addOrderBy('u.id', 'ASC');
        }

        // Пагинация
        $qb->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        $paginator = new Paginator(
            $qb->getQuery(),
            fetchJoinCollection: false
        );

        return [
            'users' => iterator_to_array(
                $paginator->getIterator(),
                false
            ),
            'total' => count($paginator),
        ];
    }
}
