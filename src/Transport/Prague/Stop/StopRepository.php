<?php

declare(strict_types=1);

namespace App\Transport\Prague\Stop;

use App\Doctrine\BaseRepository;
use App\Doctrine\NoEntityFoundException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;

/**
 * @extends BaseRepository<Stop>
 */
class StopRepository extends BaseRepository
{
	public function findById(int $id): Stop
	{
		/** @var Stop|null $stop */
		$stop = $this->doctrineRepository->findOneBy(['id' => $id]);

		if ($stop === null) {
			throw new NoEntityFoundException();
		}

		return $stop;
	}

	public function findByStopId(string $stopId): Stop
	{
		$qb = $this->createQueryBuilder();

		$qb->where($qb->expr()->eq('stop.stopId', ':stopId'));
		$qb->setParameter('stopId', $stopId);

		try {
			return $qb->getQuery()->getSingleResult();
		} catch (NoResultException $e) {
			throw new NoEntityFoundException();
		}
	}

	/**
	 * @return Stop[]
	 */
	public function findAll(): array
	{
		return $this->doctrineRepository->findAll();
	}

	/**
	 * @return array<int, string>
	 */
	public function findPairs(): array
	{
		$results = $this->findAll();
		$pairs = [];
		foreach ($results as $result) {
			$pairs[$result->getId()] = sprintf('%s (%s)', $result->getName(), $result->getStopId());
		}

		return $pairs;
	}

	/**
	 * @return array<string, string>
	 */
	public function findStopIdPairs(): array
	{
		$results = $this->findAll();
		$pairs = [];
		foreach ($results as $result) {
			$pairs[$result->getStopId()] = sprintf('%s (%s)', $result->getName(), $result->getStopId());
		}

		return $pairs;
	}

	public function getStopsCount(): int
	{
		$qb = $this->createQueryBuilder();
		$qb->select('count(stop.id)');
		return (int) $qb->getQuery()->getSingleScalarResult();
	}

	public function createQueryBuilder(): QueryBuilder
	{
		return $this->doctrineRepository->createQueryBuilder('stop');
	}
}
