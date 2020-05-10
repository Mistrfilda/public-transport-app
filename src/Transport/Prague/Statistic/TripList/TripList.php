<?php

declare(strict_types=1);

namespace App\Transport\Prague\Statistic\TripList;

use App\Doctrine\Identifier;
use App\Doctrine\IEntity;
use App\Doctrine\UpdatedAt;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

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
	 * @var DateTimeImmutable
	 * @ORM\Column(type="datetime_immutable")
	 */
	private $newestKnownPosition;

	/**
	 * @var string
	 * @ORM\Column(type="string")
	 */
	private $lastFinalStation;

	public function __construct(
		string $tripId,
		string $routeId,
		DateTimeImmutable $newestKnownPosition,
		string $lastFinalStation,
		DateTimeImmutable $now
	) {
		$this->tripId = $tripId;
		$this->routeId = $routeId;
		$this->newestKnownPosition = $newestKnownPosition;
		$this->lastFinalStation = $lastFinalStation;
		$this->updatedAt = $now;
	}

	public function update(
		DateTimeImmutable $newestKnownPosition,
		string $lastFinalStation,
		DateTimeImmutable $now
	): void {
		$this->newestKnownPosition = $newestKnownPosition;
		$this->lastFinalStation = $lastFinalStation;
		$this->updatedAt = $now;
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
}
