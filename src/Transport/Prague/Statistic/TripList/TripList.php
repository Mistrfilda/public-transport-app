<?php

declare(strict_types=1);

namespace App\Transport\Prague\Statistic\TripList;

use App\Doctrine\Identifier;
use App\Doctrine\IEntity;
use App\Doctrine\UpdatedAt;
use Doctrine\ORM\Mapping as ORM;
use Mistrfilda\Datetime\Types\DatetimeImmutable;

/**
 * @ORM\Entity
 * @ORM\Table(name="trip_list",
 *     indexes={
 *        @ORM\Index(name="trip", columns={"trip_id"}),
 *        @ORM\Index(name="routeTripIndex", columns={"trip_id", "route_id"})
 *	   },
 * )
 */
class TripList implements IEntity
{
	use Identifier;
	use UpdatedAt;

	/**
	 * @ORM\Column(type="string")
	 */
	private string $tripId;

	/**
	 * @ORM\Column(type="string")
	 */
	private string $routeId;

	/**
	 * @ORM\Column(type="datetime_immutable")
	 */
	private DateTimeImmutable $newestKnownPosition;

	/**
	 * @ORM\Column(type="string")
	 */
	private string $lastFinalStation;

	/**
	 * @ORM\Column(type="integer")
	 */
	private int $countOfStatistics;

	public function __construct(
		string $tripId,
		string $routeId,
		DateTimeImmutable $newestKnownPosition,
		string $lastFinalStation,
		DateTimeImmutable $now,
		int $countOfStatistics
	) {
		$this->tripId = $tripId;
		$this->routeId = $routeId;
		$this->newestKnownPosition = $newestKnownPosition;
		$this->lastFinalStation = $lastFinalStation;
		$this->updatedAt = $now;
		$this->countOfStatistics = $countOfStatistics;
	}

	public function update(
		DateTimeImmutable $newestKnownPosition,
		string $lastFinalStation,
		DateTimeImmutable $now,
		int $countOfStatistics
	): void {
		$this->newestKnownPosition = $newestKnownPosition;
		$this->lastFinalStation = $lastFinalStation;
		$this->updatedAt = $now;
		$this->countOfStatistics = $countOfStatistics;
	}

	public function getTripId(): string
	{
		return $this->tripId;
	}

	public function getRouteId(): string
	{
		return $this->routeId;
	}

	public function getNewestKnownPosition(): DateTimeImmutable
	{
		return $this->newestKnownPosition;
	}

	public function getLastFinalStation(): string
	{
		return $this->lastFinalStation;
	}

	public function getCountOfStatistics(): int
	{
		return $this->countOfStatistics;
	}
}
