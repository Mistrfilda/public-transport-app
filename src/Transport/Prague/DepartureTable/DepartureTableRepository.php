<?php

declare(strict_types=1);

namespace App\Transport\Prague\DepartureTable;

use App\Doctrine\BaseRepository;
use App\Doctrine\NoEntityFoundException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Ramsey\Uuid\UuidInterface;

/**
 * @extends BaseRepository<DepartureTable>
 */
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

	/**
	 * @return DepartureTable[]
	 */
	public function findAll(): array
	{
		return $this->doctrineRepository->findAll();
	}

	/**
	 * @return string[]
	 */
	public function findPairs(): array
	{
		$qb = $this->createQueryBuilder();
		$qb->innerJoin('departureTable.stop', 'stop');

		/** @var DepartureTable[] $results */
		$results = $qb->getQuery()->getResult();

		$pairs = [];
		foreach ($results as $departureTable) {
			$pairs[$departureTable->getId()->toString()] = $departureTable->getAdminFormatedName();
		}

		return $pairs;
	}

	public function createQueryBuilder(): QueryBuilder
	{
		return $this->doctrineRepository->createQueryBuilder('departureTable');
	}
}
