<?php

declare(strict_types=1);

namespace App\Transport\Prague\Statistic;

use App\Doctrine\Identifier;
use App\Doctrine\IEntity;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="trip_statistic_data",
 *     indexes={
 *        @ORM\Index(name="trip", columns={"trip_id"}),
 *        @ORM\Index(name="routeTripIndex", columns={"trip_id", "route_id", "final_station"})
 *	   },
 *     uniqueConstraints={
 *        @ORM\UniqueConstraint(name="trip_date_unique",columns={"trip_id", "date", "vehicle_id"})
 *     }
 * )
 */
class TripStatisticData implements IEntity
{
	use Identifier;

	/**
	 * @var string
	 * @ORM\Column(type="string")
	 */
	private $tripId;

	/**
	 * @var string
	 * @ORM\Column(type="string")
	 */
	private $routeId;

	/**
	 * @var string
	 * @ORM\Column(type="string")
	 */
	private $finalStation;

	/**
	 * @var bool
	 * @ORM\Column(type="boolean")
	 */
	private $wheelchairAccessible;

	/**
	 * @var DateTimeImmutable
	 * @ORM\Column(type="datetime_immutable")
	 */
	private $date;

	/**
	 * @var DateTimeImmutable
	 * @ORM\Column(type="datetime_immutable")
	 */
	private $oldestKnownPosition;

	/**
	 * @var DateTimeImmutable
	 * @ORM\Column(type="datetime_immutable")
	 */
	private $newestKnownPosition;

	/**
	 * @var int
	 * @ORM\Column(type="integer")
	 */
	private $highestDelay;

	/**
	 * @var int
	 * @ORM\Column(type="integer")
	 */
	private $averageDelay;

	/**
	 * @var string|null
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $company;

	/**
	 * @var string|null
	 * @ORM\Column(type="string", nullable=true)
	 */
	private $vehicleId;

	/**
	 * @var int
	 * @ORM\Column(type="integer")
	 */
	private $vehicleType;

	/**
	 * @var int
	 * @ORM\Column(type="integer")
	 */
	private $positionsCount;

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
		?string $company,
		?string $vehicleId,
		int $vehicleType,
		int $positionsCount
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
}
