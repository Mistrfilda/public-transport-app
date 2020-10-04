<?php

declare(strict_types=1);

namespace App\Transport\ParkingLot;

use App\Utils\Coordinates;

interface IParkingLot
{
	public function getCoordinates(): Coordinates;

	public function getName(): string;

	public function getParkingId(): string;

	public function getAddress(): string;

	public function getParkingType(): string;

	public function isParkAndRide(): bool;

	/** @return IParkingLotOccupancy[] */
	public function getParkingLotOccupancies(): array;

	public function getLastParkingLotOccupancy(): IParkingLotOccupancy;

	public function getPaymentUrl(): ?string;
}
