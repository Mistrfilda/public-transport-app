<?php

declare(strict_types=1);

namespace App\Transport\Prague\StopLine\Trip;

use App\Doctrine\BaseRepository;
use App\Doctrine\NoEntityFoundException;
use DateTimeImmutable;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;

class TripRepository extends BaseRepository
{
    public function findById(int $id): Trip
    {
        /** @var Trip|null $stop */
        $stop = $this->doctrineRepository->findOneBy(['id' => $id]);

        if ($stop === null) {
            throw new NoEntityFoundException();
        }

        return $stop;
    }

    /**
     * @return Trip[]
     */
    public function findByStop(int $stopId): array
    {
        $qb = $this->createQueryBuilder();

        $qb->where($qb->expr()->eq('trip.stop', ':stopId'));
        $qb->setParameter('stopId', $stopId);

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Trip[]
     */
    public function findByStopAndDate(int $stopId, DateTimeImmutable $date): array
    {
        $qb = $this->createQueryBuilder();

        $qb->where($qb->expr()->eq('trip.stop', ':stopId'));
        $qb->setParameter('stopId', $stopId);

        $qb->andWhere($qb->expr()->eq('trip.date', ':date'));
        $qb->setParameter('date', $date);

        return $qb->getQuery()->getResult();
    }

    /**
     * @throws NoEntityFoundException
     * @throws NonUniqueResultException
     */
    public function findByStopDateTripId(int $stopId, DateTimeImmutable $date, string $tripId): Trip
    {
        $qb = $this->createQueryBuilder();

        $qb->where($qb->expr()->eq('trip.stop', ':stopId'));
        $qb->setParameter('stopId', $stopId);

        $qb->andWhere($qb->expr()->eq('trip.date', ':date'));
        $qb->setParameter('date', $date);

        $qb->andWhere($qb->expr()->eq('trip.tripId', ':tripId'));
        $qb->setParameter('tripId', $tripId);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            throw new NoEntityFoundException();
        }
    }

    /**
     * @return Trip[]
     */
    public function findAll(): array
    {
        return $this->doctrineRepository->findAll();
    }

    public function createQueryBuilder(): QueryBuilder
    {
        return $this->doctrineRepository->createQueryBuilder('trip');
    }
}
