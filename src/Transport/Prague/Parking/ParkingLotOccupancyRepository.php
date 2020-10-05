<?php

declare(strict_types=1);

namespace App\Transport\Prague\Parking;

use App\Doctrine\BaseRepository;
use Doctrine\ORM\QueryBuilder;
use Ramsey\Uuid\UuidInterface;

/**
 * @extends BaseRepository<ParkingLot>
 */
class ParkingLotOccupancyRepository extends BaseRepository
{
	public function createQueryBuilderForDatagrid(UuidInterface $parkingLotId): QueryBuilder
	{
		$qb = $this->createQueryBuilder();
		$qb->andWhere($qb->expr()->eq('parkingLotOccupancy.parkingLot', ':id'));
		$qb->setParameter('id', $parkingLotId);
		return $qb;
	}

	public function getLastParkingDate(): string
	{
		$qb = $this->createQueryBuilder();
		$qb->select('max(parkingLotOccupancy.createdAt)');
		return $qb->getQuery()->getSingleScalarResult();
	}

	public function createQueryBuilder(): QueryBuilder
	{
		return $this->doctrineRepository->createQueryBuilder('parkingLotOccupancy');
	}
}
