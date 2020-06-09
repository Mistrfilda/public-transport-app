<?php

declare(strict_types=1);

namespace App\Transport\Prague\Vehicle;

use App\Doctrine\BaseRepository;
use App\Doctrine\NoEntityFoundException;
use App\Doctrine\OrderBy;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Ramsey\Uuid\UuidInterface;

/**
 * @extends BaseRepository<VehiclePosition>
 */
class VehiclePositionRepository extends BaseRepository
{
	public function findById(UuidInterface $id): VehiclePosition
	{
		$qb = $this->createQueryBuilder();

		$qb->where($qb->expr()->eq('vehiclePosition.id', ':id'));
		$qb->setParameter('id', $id);

		try {
			return $qb->getQuery()->getSingleResult();
		} catch (NoResultException $e) {
			throw new NoEntityFoundException();
		}
	}

	public function findLast(): ?VehiclePosition
	{
		$qb = $this->createQueryBuilder();
		$qb->orderBy('vehiclePosition.createdAt', OrderBy::DESC);
		$qb->setMaxResults(1);

		try {
			return $qb->getQuery()->getSingleResult();
		} catch (NoResultException $e) {
			return null;
		}
	}

	public function createQueryBuilder(): QueryBuilder
	{
		return $this->doctrineRepository->createQueryBuilder('vehiclePosition');
	}
}
