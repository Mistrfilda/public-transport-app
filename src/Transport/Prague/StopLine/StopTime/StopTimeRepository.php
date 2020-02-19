<?php

declare(strict_types=1);

namespace App\Transport\Prague\StopLine\StopTime;

use App\Doctrine\BaseRepository;
use App\Doctrine\NoEntityFoundException;
use DateTimeImmutable;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;

class StopTimeRepository extends BaseRepository
{
    public function findById(int $id): StopTime
    {
        /** @var StopTime|null $stop */
        $stop = $this->doctrineRepository->findOneBy(['id' => $id]);

        if ($stop === null) {
            throw new NoEntityFoundException();
        }

        return $stop;
    }

    /**
     * @return StopTime[]
     */
    public function findByStop(int $stopId): array
    {
        $qb = $this->createQueryBuilder();

        $qb->where($qb->expr()->eq('stopTime.stop', ':stopId'));
        $qb->setParameter('stopId', $stopId);

        return $qb->getQuery()->getResult();
    }

    /**
     * @return StopTime[]
     */
    public function findByStopAndDate(int $stopId, DateTimeImmutable $date): array
    {
        $qb = $this->createQueryBuilder();

        $qb->where($qb->expr()->eq('stopTime.stop', ':stopId'));
        $qb->setParameter('stopId', $stopId);

        $qb->andWhere($qb->expr()->eq('stopTime.date', ':date'));
        $qb->setParameter('date', $date);

        return $qb->getQuery()->getResult();
    }

    /**
     * @throws NoEntityFoundException
     * @throws NonUniqueResultException
     */
    public function findByStopDateTripId(int $stopId, DateTimeImmutable $date, string $tripId): StopTime
    {
        $qb = $this->createQueryBuilder();

        $qb->where($qb->expr()->eq('stopTime.stop', ':stopId'));
        $qb->setParameter('stopId', $stopId);

        $qb->andWhere($qb->expr()->eq('stopTime.date', ':date'));
        $qb->setParameter('date', $date);

        $qb->andWhere($qb->expr()->eq('stopTime.tripId', ':tripId'));
        $qb->setParameter('tripId', $tripId);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            throw new NoEntityFoundException();
        }
    }

    /**
     * @return StopTime[]
     */
    public function findAll(): array
    {
        return $this->doctrineRepository->findAll();
    }

    public function createQueryBuilder(): QueryBuilder
    {
        return $this->doctrineRepository->createQueryBuilder('stopTime');
    }
}
