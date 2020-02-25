<?php

declare(strict_types=1);

namespace App\Transport\Prague\DepartureTable;

use App\Doctrine\BaseRepository;
use App\Doctrine\NoEntityFoundException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Ramsey\Uuid\UuidInterface;

class DepartureTableRepository extends BaseRepository
{
    public function findById(UuidInterface $departureTableId): DepartureTable
    {
        $qb = $this->createQueryBuilder();

        $qb->where($qb->expr()->eq('departureTable.id', ':id'));
        $qb->setParameter('id', $departureTableId);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            throw new NoEntityFoundException();
        }
    }

    public function createQueryBuilder(): QueryBuilder
    {
        return $this->doctrineRepository->createQueryBuilder('departureTable');
    }
}
