<?php

declare(strict_types=1);

namespace App\Request;

use App\Doctrine\BaseRepository;
use App\Doctrine\NoEntityFoundException;
use App\Transport\Prague\DepartureTable\DepartureTable;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Mistrfilda\Datetime\Types\DatetimeImmutable;
use Ramsey\Uuid\UuidInterface;

/**
 * @extends BaseRepository<Request>
 */
class RequestRepository extends BaseRepository
{
	public function findById(int $id): Request
	{
		/** @var Request|null $request */
		$request = $this->doctrineRepository->findOneBy(['id' => $id]);

		if ($request === null) {
			throw new NoEntityFoundException();
		}

		return $request;
	}

	/**
	 * @return Request[]
	 */
	public function findAll(): array
	{
		return $this->doctrineRepository->findAll();
	}

	public function findLastRequestByType(string $type, DateTimeImmutable $now): ?Request
	{
		$qb = $this->createQueryBuilder();

		$qb->andWhere($qb->expr()->eq('request.type', ':type'));
		$qb->setParameter('type', $type);

		$qb->andWhere($qb->expr()->isNull('request.finishedAt'));
		$qb->andWhere($qb->expr()->isNull('request.failedAt'));

		$qb->orderBy('request.id', 'desc');
		$qb->setMaxResults(1);

		$qb->andWhere($qb->expr()->gte('request.createdAt', ':createdAt'));
		$qb->setParameter('createdAt', $now->modify('- 4 hour'));

		try {
			return $qb->getQuery()->getSingleResult();
		} catch (NoResultException $e) {
			return null;
		}
	}

	public function findLastRequestByTypeAndDepartureTable(
		string $type,
		DepartureTable $departureTable,
		DateTimeImmutable $now
	): ?Request {
		$qb = $this->createQueryBuilder();

		$qb->andWhere($qb->expr()->eq('request.type', ':type'));
		$qb->setParameter('type', $type);

		$qb->andWhere($qb->expr()->isNull('request.finishedAt'));
		$qb->andWhere($qb->expr()->isNull('request.failedAt'));

		$qb->andWhere($qb->expr()->eq('request.pragueDepartureTable', ':departureTable'));
		$qb->setParameter('departureTable', $departureTable);

		$qb->orderBy('request.id', 'desc');
		$qb->setMaxResults(1);

		$qb->andWhere($qb->expr()->gte('request.createdAt', ':createdAt'));
		$qb->setParameter('createdAt', $now->modify('- 12 hour'));

		try {
			return $qb->getQuery()->getSingleResult();
		} catch (NoResultException $e) {
			return null;
		}
	}

	public function getLastRandomDepartureTableDownloadTime(): ?string
	{
		$qb = $this->createQueryBuilder();
		$qb->select('max(request.finishedAt)');
		$qb->andWhere($qb->expr()->eq('request.type', ':type'));
		$qb->setParameter('type', RequestType::PRAGUE_DEPARTURE_TABLE);
		try {
			return $qb->getQuery()->getSingleScalarResult();
		} catch (NoResultException $e) {
			return null;
		}
	}

	public function getLastDepartureTableDownloadTime(UuidInterface $departureTableId): ?string
	{
		$qb = $this->createQueryBuilder();
		$qb->select('max(request.finishedAt)');
		$qb->andWhere($qb->expr()->eq('request.type', ':type'));
		$qb->setParameter('type', RequestType::PRAGUE_DEPARTURE_TABLE);

		$qb->andWhere($qb->expr()->eq('request.pragueDepartureTable', ':departureTableId'));
		$qb->setParameter('departureTableId', $departureTableId);
		try {
			return $qb->getQuery()->getSingleScalarResult();
		} catch (NoResultException $e) {
			return null;
		}
	}

	public function createQueryBuilder(): QueryBuilder
	{
		return $this->doctrineRepository->createQueryBuilder('request');
	}
}
