<?php

declare(strict_types=1);

namespace App\Transport\Prague\Statistic;

use App\Doctrine\BaseRepository;
use App\Doctrine\OrderBy;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
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

	/**
	 * @throws NoResultException
	 * @throws NonUniqueResultException
	 */
	public function findByTripIdSingle(string $tripId): TripStatisticData
	{
		$qb = $this->createQueryBuilder();

		$qb->andWhere($qb->expr()->eq('tripStatistic.tripId', ':tripId'));
		$qb->setParameter('tripId', $tripId);

		$qb->orderBy('tripStatistic.date', OrderBy::DESC);
		$qb->setMaxResults(1);

		return $qb->getQuery()->getSingleResult();
	}

	public function getCountTripsByTripId(string $tripId): int
	{
		$qb = $this->createQueryBuilder();
		$qb->select('count(tripStatistic.id)');

		$qb->andWhere($qb->expr()->eq('tripStatistic.tripId', ':tripId'));
		$qb->setParameter('tripId', $tripId);

		$qb->setMaxResults(1);

		return (int) $qb->getQuery()->getSingleScalarResult();
	}

	public function getAvgTripDelay(string $tripId): float
	{
		$qb = $this->createQueryBuilder();
		$qb->select('avg(tripStatistic.averageDelay)');

		$qb->andWhere($qb->expr()->eq('tripStatistic.tripId', ':tripId'));
		$qb->setParameter('tripId', $tripId);

		$qb->groupBy('tripStatistic.tripId');

		$qb->setMaxResults(1);

		return (float) $qb->getQuery()->getSingleScalarResult();
	}

	/**
	 * @return array<array<string, string>>
	 */
	public function getVehicleTypeCountByTripId(string $tripId): array
	{
		$qb = $this->createQueryBuilder();

		$qb->select('tripStatistic.vehicleId, count(tripStatistic.id) as count');

		$qb->andWhere($qb->expr()->eq('tripStatistic.tripId', ':tripId'));
		$qb->setParameter('tripId', $tripId);

		$qb->groupBy('tripStatistic.tripId, tripStatistic.vehicleId');

		return $qb->getQuery()->getResult();
	}

	/**
	 * @return array<array<string, string>>
	 */
	public function findTripList(): array
	{
		$qb = $this->createQueryBuilder();
		$qb->select(
			'tripStatistic.tripId, tripStatistic.routeId, max(tripStatistic.finalStation) as finalStation, max(tripStatistic.newestKnownPosition) as newestKnownPosition, count(tripStatistic.id) as rowCount'
		);
		$qb->groupBy('tripStatistic.tripId, tripStatistic.routeId');

		return $qb->getQuery()->getResult();
	}

	public function createQueryBuilder(): QueryBuilder
	{
		return $this->doctrineRepository->createQueryBuilder('tripStatistic');
	}
}
