<?php

declare(strict_types=1);

namespace App\Request;

use App\Doctrine\BaseRepository;
use App\Doctrine\NoEntityFoundException;
use App\Transport\Prague\DepartureTable\DepartureTable;
use DateTimeImmutable;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;

class RequestRepository extends BaseRepository
{
    public function findById(int $id): Request
    {
        /** @var Request|null $request */
        $request = $this->doctrineRepository->findOneBy(['id' => $id]);

        if ($request === null) {
            throw new NoEntityFoundException();
        }

        return $request;
    }

    public function findLastRequestByType(string $type, DateTimeImmutable $now): ?Request
    {
        $qb = $this->createQueryBuilder();

        $qb->andWhere($qb->expr()->eq('request.type', ':type'));
        $qb->setParameter('type', $type);

        $qb->andWhere($qb->expr()->isNull('request.finishedAt'));
        $qb->andWhere($qb->expr()->isNull('request.failedAt'));

        $qb->orderBy('request.id', 'desc');
        $qb->setMaxResults(1);

        $qb->andWhere($qb->expr()->gte('request.createdAt', ':createdAt'));
        $qb->setParameter('createdAt', $now->modify('- 4 hour'));

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    public function findLastRequestByTypeAndDepartureTable(
        string $type,
        DepartureTable $departureTable,
        DateTimeImmutable $now
    ): ?Request {
        $qb = $this->createQueryBuilder();

        $qb->andWhere($qb->expr()->eq('request.type', ':type'));
        $qb->setParameter('type', $type);

        $qb->andWhere($qb->expr()->isNull('request.finishedAt'));
        $qb->andWhere($qb->expr()->isNull('request.failedAt'));

        $qb->andWhere($qb->expr()->eq('request.pragueDepartureTable', ':departureTable'));
        $qb->setParameter('departureTable', $departureTable);

        $qb->orderBy('request.id', 'desc');
        $qb->setMaxResults(1);

        $qb->andWhere($qb->expr()->gte('request.createdAt', ':createdAt'));
        $qb->setParameter('createdAt', $now->modify('- 12 hour'));

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    public function createQueryBuilder(): QueryBuilder
    {
        return $this->doctrineRepository->createQueryBuilder('request');
    }
}
