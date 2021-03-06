<?php

declare(strict_types=1);

namespace App\Transport\Prague\Statistic;

use App\Doctrine\Identifier;
use App\Doctrine\IEntity;
use Doctrine\ORM\Mapping as ORM;
use Mistrfilda\Datetime\Types\DatetimeImmutable;

/**
 * @ORM\Entity
 * @ORM\Table(name="trip_statistic_data",
 *     indexes={
 *        @ORM\Index(name="trip", columns={"trip_id"}),
 *        @ORM\Index(name="trip_route", columns={"trip_id", "route_id"}),
 *        @ORM\Index(name="routeTripIndex", columns={"trip_id", "route_id", "final_station"})
 *	   },
 *     uniqueConstraints={
 *        @ORM\UniqueConstraint(name="trip_date_unique",columns={"trip_id", "date", "vehicle_id", "company", "wheelchair_accessible"})
 *     }
 * )
 */
class TripStatisticData implements IEntity
{
	use Identifier;

	/**
	 * @ORM\Column(type="string")
	 */
	private string $tripId;

	/**
	 * @ORM\Column(type="string")
	 */
	private string $routeId;

	/**
	 * @ORM\Column(type="string")
	 */
	private string $finalStation;

	/**
	 * @ORM\Column(type="boolean")
	 */
	private bool $wheelchairAccessible;

	/**
	 * @ORM\Column(type="datetime_immutable")
	 */
	private DateTimeImmutable $date;

	/**
	 * @ORM\Column(type="datetime_immutable")
	 */
	private DateTimeImmutable $oldestKnownPosition;

	/**
	 * @ORM\Column(type="datetime_immutable")
	 */
	private DateTimeImmutable $newestKnownPosition;

	/**
	 * @ORM\Column(type="integer")
	 */
	private int $highestDelay;

	/**
	 * @ORM\Column(type="integer")
	 */
	private int $averageDelay;

	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private ?int $lastPositionDelay;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 */
	private ?string $company = null;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 */
	private ?string $vehicleId = null;

	/**
	 * @ORM\Column(type="integer")
	 */
	private int $vehicleType;

	/**
	 * @ORM\Column(type="integer")
	 */
	private int $positionsCount;

	/**
	 * @ORM\Column(type="string")
	 */
	private string $dayName;

	/**
	 * @ORM\Column(type="boolean")
	 */
	private bool $isCzechHoliday;

	public function __construct(
		string $tripId,
		string $routeId,
		string $finalStation,
		bool $wheelchairAccessible,
		DateTimeImmutable $date,
		DateTimeImmutable $oldestKnownPosition,
		DateTimeImmutable $newestKnownPosition,
		int $highestDelay,
		int $averageDelay,
		?int $lastPositionDelay,
		?string $company,
		?string $vehicleId,
		int $vehicleType,
		int $positionsCount,
		bool $isCzechHoliday
	) {
		$this->tripId = $tripId;
		$this->routeId = $routeId;
		$this->finalStation = $finalStation;
		$this->wheelchairAccessible = $wheelchairAccessible;
		$this->date = $date;
		$this->oldestKnownPosition = $oldestKnownPosition;
		$this->newestKnownPosition = $newestKnownPosition;
		$this->highestDelay = $highestDelay;
		$this->averageDelay = $averageDelay;
		$this->company = $company;
		$this->vehicleId = $vehicleId;
		$this->vehicleType = $vehicleType;
		$this->positionsCount = $positionsCount;
		$this->isCzechHoliday = $isCzechHoliday;
		$this->dayName = $date->format('D');
		$this->lastPositionDelay = $lastPositionDelay;
	}

	public function updateDate(bool $isCzechHoliday): void
	{
		$this->dayName = $this->date->format('D');
		$this->isCzechHoliday = $isCzechHoliday;
	}

	public function getTripId(): string
	{
		return $this->tripId;
	}

	public function getRouteId(): string
	{
		return $this->routeId;
	}

	public function getFinalStation(): string
	{
		return $this->finalStation;
	}

	public function isWheelchairAccessible(): bool
	{
		return $this->wheelchairAccessible;
	}

	public function getDate(): DateTimeImmutable
	{
		return $this->date;
	}

	public function getOldestKnownPosition(): DateTimeImmutable
	{
		return $this->oldestKnownPosition;
	}

	public function getNewestKnownPosition(): DateTimeImmutable
	{
		return $this->newestKnownPosition;
	}

	public function getHighestDelay(): int
	{
		return $this->highestDelay;
	}

	public function getAverageDelay(): int
	{
		return $this->averageDelay;
	}

	public function getCompany(): ?string
	{
		return $this->company;
	}

	public function getVehicleId(): ?string
	{
		return $this->vehicleId;
	}

	public function getVehicleType(): int
	{
		return $this->vehicleType;
	}

	public function getPositionsCount(): int
	{
		return $this->positionsCount;
	}

	public function getDayName(): string
	{
		return $this->dayName;
	}

	public function isCzechHoliday(): bool
	{
		return $this->isCzechHoliday;
	}

	public function getLastPositionDelay(): ?int
	{
		return $this->lastPositionDelay;
	}
}
