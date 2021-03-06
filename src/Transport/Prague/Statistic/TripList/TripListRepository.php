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
		$qb = $this->createQueryBuilder();

		$offset = 0;
		$maxResults = 5000;
		$pairs = [];

		while (true) {
			$qb->setMaxResults($maxResults);
			$qb->setFirstResult($offset);

			/** @var TripList[] $results */
			$results = $qb->getQuery()->getResult();

			if (count($results) === 0) {
				break;
			}

			foreach ($results as $result) {
				$pairs[$result->getTripId()] = $result->getId();
			}
			$this->entityManager->clear();

			$offset += $maxResults;
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

	public function getTripListCount(): int
	{
		$qb = $this->createQueryBuilder();
		$qb->select('count(tripList.id)');
		return (int) $qb->getQuery()->getSingleScalarResult();
	}

	public function getTripListLineCount(): int
	{
		$qb = $this->createQueryBuilder();
		$qb->select('count(tripList.id)');
		$qb->groupBy('tripList.routeId');
		return count($qb->getQuery()->getResult());
	}
}
