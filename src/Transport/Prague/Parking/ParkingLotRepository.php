<?php

declare(strict_types=1);

namespace App\Transport\Prague\Parking;

use App\Doctrine\BaseRepository;
use App\Doctrine\NoEntityFoundException;
use Doctrine\ORM\QueryBuilder;
use Ramsey\Uuid\UuidInterface;

/**
 * @extends BaseRepository<ParkingLot>
 */
class ParkingLotRepository extends BaseRepository
{
	public function getById(UuidInterface $id): ParkingLot
	{
		/** @var ParkingLot|null $parkingLot */
		$parkingLot = $this->doctrineRepository->findOneBy(['id' => $id]);

		if ($parkingLot === null) {
			throw new NoEntityFoundException();
		}

		return $parkingLot;
	}

	public function getByParkingId(string $parkingId): ParkingLot
	{
		/** @var ParkingLot|null $parkingLot */
		$parkingLot = $this->doctrineRepository->findOneBy(['parkingId' => $parkingId]);

		if ($parkingLot === null) {
			throw new NoEntityFoundException();
		}

		return $parkingLot;
	}

	/**
	 * @return ParkingLot[]
	 */
	public function findAll(): array
	{
		return $this->doctrineRepository->findAll();
	}

	/**
	 * @param UuidInterface[] $ids
	 * @return ParkingLot[]
	 */
	public function findByIds(array $ids): array
	{
		$qb = $this->doctrineRepository->createQueryBuilder('parkingLot');
		$qb->andWhere($qb->expr()->in('parkingLot.id', $ids));

		return $qb->getQuery()->getResult();
	}

	public function createQueryBuilderForDatagrid(): QueryBuilder
	{
		$qb = $this->doctrineRepository->createQueryBuilder('parkingLot');
		$qb->innerJoin('parkingLot.lastParkingLotOccupancy', 'lastParkingLotOccupancy');
		return $qb;
	}

	public function getParkingLotsCount(): int
	{
		$qb = $this->createQueryBuilder();
		$qb->select('count(parkingLot.id)');
		return (int) $qb->getQuery()->getSingleScalarResult();
	}

	public function createQueryBuilder(): QueryBuilder
	{
		return $this->doctrineRepository->createQueryBuilder('parkingLot');
	}
}
