<?php

declare(strict_types=1);

namespace App\Transport\Prague\Parking;

use App\Doctrine\CreatedAt;
use App\Doctrine\IEntity;
use App\Doctrine\SimpleUuid;
use App\Transport\ParkingLot\IParkingLot;
use App\Transport\ParkingLot\IParkingLotOccupancy;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity
 * @ORM\Table(name="prague_parking_lot_occupancy")
 */
class ParkingLotOccupancy implements IEntity, IParkingLotOccupancy
{
	use CreatedAt;
	use SimpleUuid;

	/**
	 * @ORM\ManyToOne(targetEntity="App\Transport\Prague\Parking\ParkingLot", inversedBy="parkingLotOccupancies")
	 */
	private ParkingLot $parkingLot;

	/**
	 * @ORM\Column(type="integer")
	 */
	private int $totalSpaces;

	/**
	 * @ORM\Column(type="integer")
	 */
	private int $freeSpaces;

	/**
	 * @ORM\Column(type="integer")
	 */
	private int $occupiedSpaces;

	public function __construct(
		DateTimeImmutable $now,
		ParkingLot $parkingLot,
		int $totalSpaces,
		int $freeSpaces,
		int $occupiedSpaces
	) {
		$this->id = Uuid::uuid4();
		$this->parkingLot = $parkingLot;
		$this->totalSpaces = $totalSpaces;
		$this->freeSpaces = $freeSpaces;
		$this->occupiedSpaces = $occupiedSpaces;
		$this->createdAt = $now;
	}

	public function getParkingLot(): IParkingLot
	{
		return $this->parkingLot;
	}

	public function getTotalSpaces(): int
	{
		return $this->totalSpaces;
	}

	public function getFreeSpaces(): int
	{
		return $this->freeSpaces;
	}

	public function getOccupiedSpaces(): int
	{
		return $this->occupiedSpaces;
	}
}
