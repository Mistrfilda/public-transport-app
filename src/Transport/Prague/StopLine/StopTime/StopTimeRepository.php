<?php

declare(strict_types=1);

namespace App\Transport\Prague\StopLine\StopTime;

use App\Doctrine\BaseRepository;
use App\Doctrine\NoEntityFoundException;
use App\Doctrine\OrderBy;
use App\Transport\Prague\StopLine\Trip\Trip;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Mistrfilda\Datetime\Types\DatetimeImmutable;

/**
 * @extends BaseRepository<StopTime>
 */
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
	 * @return StopTime[]
	 */
	public function findForDepartureTable(int $stopId, DateTimeImmutable $now): array
	{
		$qb = $this->doctrineRepository->createQueryBuilder('stopTime', 'stopTime.dateTripId');

		$qb->andWhere($qb->expr()->eq('stopTime.stop', ':stopId'));
		$qb->setParameter('stopId', $stopId);

		$qb->andWhere($qb->expr()->gte('stopTime.departureTime', ':now'));
		$qb->setParameter('now', $now->modify('- 2 hours'));

		$qb->orderBy('stopTime.departureTime', OrderBy::ASC);

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
	 * @return array<string, int>
	 */
	public function findIdsByDate(int $stopId, DateTimeImmutable $date): array
	{
		$qb = $this->createQueryBuilder();

		$qb->where($qb->expr()->eq('stopTime.stop', ':stopId'));
		$qb->setParameter('stopId', $stopId);

		$qb->andWhere($qb->expr()->eq('stopTime.date', ':date'));
		$qb->setParameter('date', $date);

		$pairs = [];
		$stopTimes = $qb->getQuery()->getResult();

		/** @var StopTime $stopTime */
		foreach ($stopTimes as $stopTime) {
			$pairs[$stopTime->getTripId()] = $stopTime->getId();
		}

		return $pairs;
	}

	/**
	 * @param array<int|string,int> $stopTimesIds
	 * @return StopTime[]
	 */
	public function findByIds(array $stopTimesIds): array
	{
		$qb = $this->createQueryBuilder();

		$qb->andWhere($qb->expr()->in('stopTime.id', $stopTimesIds));

		return $qb->getQuery()->getResult();
	}

	/**
	 * @return StopTime[]
	 */
	public function findAll(): array
	{
		return $this->doctrineRepository->findAll();
	}

	/**
	 * @return StopTime[]
	 */
	public function findAllSorted(): array
	{
		return $this->doctrineRepository->findBy([], ['departureTime' => OrderBy::ASC]);
	}

	/**
	 * Return array of destinations, divided by '~'
	 * @return array<int, string>
	 */
	public function findDepartureTablesDestinations(
		?DateTimeImmutable $from = null,
		?DateTimeImmutable $to = null
	): array {
		$qb = $this->doctrineRepository->createQueryBuilder('stopTime', 'stopTime.id');

		$qb->select('stop.id, GROUP_CONCAT(DISTINCT trip.tripHeadsign SEPARATOR \'~\') as tripHeadsign');

		$qb->innerJoin('stopTime.stop', 'stop');
		$qb->innerJoin(Trip::class, 'trip', Join::WITH, 'trip.dateTripId = stopTime.dateTripId');

		$qb->groupBy('stop.id');
		$qb->andWhere($qb->expr()->gte('stopTime.departureTime', ':from'));
		$qb->setParameter('from', $from);

		$qb->andWhere($qb->expr()->lt('stopTime.departureTime', ':to'));
		$qb->setParameter('to', $to);

		$data = [];

		/** @var array<int, array{id: int, tripHeadsign: string}> $queryResult */
		$queryResult = $qb->getQuery()->getResult();

		foreach ($queryResult as $result) {
			$data[$result['id']] = $result['tripHeadsign'];
		}

		return $data;
	}

	/**
	 * Return array of lines, divided by '~'
	 * @return array<int, string>
	 */
	public function findDepartureTablesLines(
		?DateTimeImmutable $from = null,
		?DateTimeImmutable $to = null
	): array {
		$qb = $this->doctrineRepository->createQueryBuilder('stopTime', 'stopTime.id');

		$qb->select('stop.id, GROUP_CONCAT(DISTINCT trip.lineNumber SEPARATOR \'~\') as lineNumber');

		$qb->innerJoin('stopTime.stop', 'stop');
		$qb->innerJoin(Trip::class, 'trip', Join::WITH, 'trip.dateTripId = stopTime.dateTripId');

		$qb->andWhere($qb->expr()->gte('stopTime.departureTime', ':from'));
		$qb->setParameter('from', $from);

		$qb->andWhere($qb->expr()->lt('stopTime.departureTime', ':to'));
		$qb->setParameter('to', $to);

		$qb->groupBy('stop.id');

		$data = [];

		/** @var array<int, array{id: int, lineNumber: string}> $queryResult */
		$queryResult = $qb->getQuery()->getResult();

		foreach ($queryResult as $result) {
			$data[$result['id']] = $result['lineNumber'];
		}

		return $data;
	}

	public function createQueryBuilder(): QueryBuilder
	{
		return $this->doctrineRepository->createQueryBuilder('stopTime');
	}
}
