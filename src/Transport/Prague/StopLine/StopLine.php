<?php

declare(strict_types=1);

namespace App\Transport\Prague\StopLine;

use App\Transport\Prague\Stop\Stop;
use App\Transport\Prague\Vehicle\Vehicle;
use App\Transport\Stop\IStop;
use App\Transport\StopLine\IStopLine;
use App\Transport\Vehicle\IVehicle;
use Mistrfilda\Datetime\Types\DatetimeImmutable;

class StopLine implements IStopLine
{
	private IStop $stop;

	private DatetimeImmutable $arrivalTime;

	private DateTimeImmutable $departureTime;

	private string $tripId;

	private string $lineNumber;

	private string $finalDestination;

	private ?IVehicle $vehicle = null;

	private DateTimeImmutable $now;

	private bool $hasStatistics;

	public function __construct(
		Stop $stop,
		DateTimeImmutable $arrivalTime,
		DateTimeImmutable $departureTime,
		string $tripId,
		string $lineNumber,
		string $finalDestination,
		?Vehicle $vehicle,
		DateTimeImmutable $now,
		bool $hasStatistics
	) {
		$this->stop = $stop;
		$this->arrivalTime = $arrivalTime;
		$this->departureTime = $departureTime;
		$this->tripId = $tripId;
		$this->lineNumber = $lineNumber;
		$this->finalDestination = $finalDestination;
		$this->vehicle = $vehicle;
		$this->now = $now;
		$this->hasStatistics = $hasStatistics;
	}

	public function getStop(): IStop
	{
		return $this->stop;
	}

	public function getArrivalTime(): DateTimeImmutable
	{
		return $this->arrivalTime;
	}

	public function getDepartureTime(): DateTimeImmutable
	{
		return $this->departureTime;
	}

	public function getTripId(): string
	{
		return $this->tripId;
	}

	public function getLineNumber(): string
	{
		return $this->lineNumber;
	}

	public function getFinalDestination(): string
	{
		return $this->finalDestination;
	}

	public function getVehicle(): ?IVehicle
	{
		return $this->vehicle;
	}

	public function hasVehicle(): bool
	{
		return $this->vehicle !== null;
	}

	public function hasVehicleLeft(): bool
	{
		if ($this->getRealDepartureTime()->getTimestamp() > $this->now->getTimestamp()) {
			return false;
		}

		return true;
	}

	public function getRealDepartureTime(): DateTimeImmutable
	{
		$departureTime = $this->getDepartureTime();
		$vehicle = $this->getVehicle();

		if ($vehicle !== null && $vehicle->getDelayInSeconds() > 0) {
			return $departureTime->modify('+ ' . $vehicle->getDelayInSeconds() . ' seconds');
		}

		return $departureTime;
	}

	public function isNearDeparture(): bool
	{
		$realDepartureTime = $this->getRealDepartureTime();

		if ($this->now->modify('+ 2 minutes')->getTimestamp() > $realDepartureTime->getTimestamp()) {
			return true;
		}

		return false;
	}

	public function hasBigDelay(): bool
	{
		$vehicle = $this->getVehicle();
		if ($vehicle !== null && $vehicle->getDelayInSeconds() > 240 && $this->isNearDeparture() === false) {
			return true;
		}

		return false;
	}

	public function hasStatistics(): bool
	{
		return $this->hasStatistics;
	}
}
