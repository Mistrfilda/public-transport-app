<?php

declare(strict_types=1);

namespace App\Transport\Prague\Statistic;

use App\Doctrine\BaseRepository;
use App\Doctrine\OrderBy;
use Doctrine\ORM\QueryBuilder;

class TripStatisticDataRepository extends BaseRepository
{
    /**
     * @return TripStatisticData[]
     */
    public function findByTripId(string $tripId, ?int $limit = 20): array
    {
        $qb = $this->createQueryBuilder();

        $qb->andWhere($qb->expr()->eq('tripStatistic.tripId', ':tripId'));
        $qb->setParameter('tripId', $tripId);

        $qb->orderBy('tripStatistic.date', OrderBy::DESC);

        if ($limit !== null) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }

    public function createQueryBuilder(): QueryBuilder
    {
        return $this->doctrineRepository->createQueryBuilder('tripStatistic');
    }
}
