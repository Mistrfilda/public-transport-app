<?php

declare(strict_types=1);

namespace App\Transport\StopLine;

use App\Transport\Stop\IStop;
use App\Transport\Vehicle\IVehicle;
use Mistrfilda\Datetime\Types\DatetimeImmutable;

interface IStopLine
{
	public function getStop(): IStop;

	public function getArrivalTime(): DateTimeImmutable;

	public function getDepartureTime(): DateTimeImmutable;

	public function getTripId(): string;

	public function getLineNumber(): string;

	public function getFinalDestination(): string;

	/** Nullable, vehicle can be at depot! */
	public function getVehicle(): ?IVehicle;

	public function hasVehicle(): bool;

	public function hasVehicleLeft(): bool;

	public function getRealDepartureTime(): DateTimeImmutable;

	public function isNearDeparture(): bool;

	public function hasBigDelay(): bool;

	public function hasStatistics(): bool;
}
