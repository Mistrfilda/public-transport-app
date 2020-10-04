<?php

declare(strict_types=1);

namespace App\Transport\ParkingLot;

use DateTimeImmutable;

interface IParkingLotOccupancy
{
	public function getCreatedAt(): DateTimeImmutable;

	public function getTotalSpaces(): int;

	public function getFreeSpaces(): int;

	public function getOccupiedSpaces(): int;

	public function getParkingLot(): IParkingLot;
}
