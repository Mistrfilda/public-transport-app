<?php

declare(strict_types=1);

namespace App\Transport\Prague\StopLine\Trip;

use App\Doctrine\Identifier;
use App\Doctrine\IEntity;
use App\Transport\Prague\Stop\Stop;
use Doctrine\ORM\Mapping as ORM;
use Mistrfilda\Datetime\Types\DatetimeImmutable;

/**
 * @ORM\Entity()
 * @ORM\Table(name="prague_trip",
 *     indexes={
 *        @ORM\Index(name="date_trip_id_index", columns={"date_trip_id"}),
 *        @ORM\Index(name="date_trip_import", columns={"date_trip_id", "date"})
 *	   },
 *     uniqueConstraints={
 *        @ORM\UniqueConstraint(name="date_trip_id_unique",columns={"date_trip_id"})
 *     }
 * )
 */
class Trip implements IEntity
{
	use Identifier;

	/**
	 * @ORM\ManyToOne(targetEntity="App\Transport\Prague\Stop\Stop")
	 */
	private Stop $stop;

	/**
	 * @ORM\Column(type="string")
	 */
	private string $serviceId;

	/**
	 * @ORM\Column(type="string")
	 */
	private string $tripId;

	/**
	 * @ORM\Column(type="string")
	 */
	private string $dateTripId;

	/**
	 * @ORM\Column(type="string")
	 */
	private string $tripHeadsign;

	/**
	 * @ORM\Column(type="string")
	 */
	private bool $wheelchairAccessible;

	/**
	 * @ORM\Column(type="date_immutable")
	 */
	private DateTimeImmutable $date;

	/**
	 * @ORM\Column(type="string")
	 */
	private string $lineNumber;

	public function __construct(
		Stop $stop,
		string $serviceId,
		string $tripId,
		string $tripHeadsign,
		bool $wheelchairAccessible,
		DateTimeImmutable $date,
		string $lineNumber
	) {
		$this->stop = $stop;
		$this->serviceId = $serviceId;
		$this->tripId = $tripId;
		$this->tripHeadsign = $tripHeadsign;
		$this->wheelchairAccessible = $wheelchairAccessible;
		$this->date = $date;
		$this->lineNumber = $lineNumber;

		$this->dateTripId = $date->getTimestamp() . '_' . $tripId;
	}

	public function updateTrip(
		string $tripHeadsign,
		bool $wheelchairAccessible
	): void {
		$this->tripHeadsign = $tripHeadsign;
		$this->wheelchairAccessible = $wheelchairAccessible;
	}

	public function getStop(): Stop
	{
		return $this->stop;
	}

	public function getServiceId(): string
	{
		return $this->serviceId;
	}

	public function getTripId(): string
	{
		return $this->tripId;
	}

	public function getTripHeadsign(): string
	{
		return $this->tripHeadsign;
	}

	public function isWheelchairAccessible(): bool
	{
		return $this->wheelchairAccessible;
	}

	public function getDate(): DateTimeImmutable
	{
		return $this->date;
	}

	public function getDateTripId(): string
	{
		return $this->dateTripId;
	}

	public function getLineNumber(): string
	{
		return $this->lineNumber;
	}
}
