<?php

declare(strict_types=1);

namespace App\Transport\Prague\Statistic\TripList;

use App\Doctrine\BaseRepository;
use App\Doctrine\NoEntityFoundException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;

/**
 * @extends BaseRepository<TripList>
 */
class TripListRepository extends BaseRepository
{
	public function findById(int $id): TripList
	{
		/** @var TripList|null $tripList */
		$tripList = $this->doctrineRepository->findOneBy(['id' => $id]);

		if ($tripList === null) {
			throw new NoEntityFoundException();
		}

		return $tripList;
	}

	public function findByStopDateTripId(string $tripId, string $routeId): TripList
	{
		$qb = $this->createQueryBuilder();

		$qb->where($qb->expr()->eq('tripList.tripId', ':tripId'));
		$qb->setParameter('tripId', $tripId);

		$qb->andWhere($qb->expr()->eq('tripList.routeId', ':routeId'));
		$qb->setParameter('routeId', $routeId);

		try {
			return $qb->getQuery()->getSingleResult();
		} catch (NoResultException $e) {
			throw new NoEntityFoundException();
		}
	}

	/**
	 * @return array<string, int>
	 */
	public function findTripIdPairs(): array
	{
		$results = $this->findAll();
		$pairs = [];
		foreach ($results as $result) {
			$pairs[$result->getTripId()] = $result->getId();
		}

		return $pairs;
	}

	/**
	 * @return TripList[]
	 */
	public function findAll(): array
	{
		return $this->doctrineRepository->findAll();
	}

	public function createQueryBuilder(): QueryBuilder
	{
		return $this->doctrineRepository->createQueryBuilder('tripList');
	}
}
