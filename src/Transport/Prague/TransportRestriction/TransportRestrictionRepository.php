<?php

declare(strict_types=1);

namespace App\Transport\Prague\TransportRestriction;

use App\Doctrine\BaseRepository;
use App\Doctrine\NoEntityFoundException;
use App\Transport\TransportRestriction\TransportRestrictionType;
use Doctrine\ORM\QueryBuilder;
use Ramsey\Uuid\UuidInterface;

/**
 * @extends BaseRepository<TransportRestriction>
 */
class TransportRestrictionRepository extends BaseRepository
{
	public function getById(UuidInterface $id): TransportRestriction
	{
		/** @var TransportRestriction|null $transportRestriction */
		$transportRestriction = $this->doctrineRepository->findOneBy(['id' => $id]);

		if ($transportRestriction === null) {
			throw new NoEntityFoundException();
		}

		return $transportRestriction;
	}

	public function getTransportRestrictionId(string $id): TransportRestriction
	{
		/** @var TransportRestriction|null $transportRestriction */
		$transportRestriction = $this->doctrineRepository->findOneBy(['transportRestrictionId' => $id]);

		if ($transportRestriction === null) {
			throw new NoEntityFoundException();
		}

		return $transportRestriction;
	}

	/**
	 * @return TransportRestriction[]
	 */
	public function findAll(): array
	{
		return $this->doctrineRepository->findAll();
	}

	/**
	 * @param UuidInterface[] $ids
	 * @return TransportRestriction[]
	 */
	public function findByIds(array $ids): array
	{
		$qb = $this->doctrineRepository->createQueryBuilder('transportRestriction');
		$qb->andWhere($qb->expr()->in('transportRestriction.id', $ids));

		return $qb->getQuery()->getResult();
	}

	/**
	 * @return TransportRestriction[]
	 */
	public function findActive(): array
	{
		$qb = $this->doctrineRepository->createQueryBuilder(
			'transportRestriction',
			'transportRestriction.transportRestrictionId'
		);

		$qb->andWhere($qb->expr()->eq('transportRestriction.active', ':active'));
		$qb->setParameter('active', true);

		return $qb->getQuery()->getResult();
	}

	/**
	 * @return array<string, TransportRestriction>
	 */
	public function findActiveByType(string $type): array
	{
		TransportRestrictionType::exists($type);
		$qb = $this->doctrineRepository->createQueryBuilder(
			'transportRestriction',
			'transportRestriction.transportRestrictionId'
		);

		$qb->andWhere($qb->expr()->eq('transportRestriction.active', ':active'));
		$qb->setParameter('active', true);

		$qb->andWhere($qb->expr()->eq('transportRestriction.type', ':type'));
		$qb->setParameter('type', $type);

		return $qb->getQuery()->getResult();
	}

	public function createQueryBuilder(): QueryBuilder
	{
		return $this->doctrineRepository->createQueryBuilder('transportRestriction');
	}
}
