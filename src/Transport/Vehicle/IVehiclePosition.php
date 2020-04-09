<?php

declare(strict_types=1);

namespace App\Transport\Vehicle;

use DateTimeImmutable;
use Ramsey\Uuid\UuidInterface;

interface IVehiclePosition
{
	public function getId(): UuidInterface;

	public function getCreatedAt(): DateTimeImmutable;

	public function getCity(): string;

	/**
	 * @return IVehicle[]
	 */
	public function getVehicles(): array;
}
