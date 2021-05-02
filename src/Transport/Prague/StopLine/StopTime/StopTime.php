<?php

declare(strict_types=1);

namespace App\Transport\Prague\StopLine\StopTime;

use App\Doctrine\Identifier;
use App\Doctrine\IEntity;
use App\Transport\Prague\Stop\Stop;
use Doctrine\ORM\Mapping as ORM;
use Mistrfilda\Datetime\Types\DatetimeImmutable;

/**
 * @ORM\Entity()
 * @ORM\Table(name="prague_stop_time",
 *     indexes={
 *        @ORM\Index(name="date_trip_id_index", columns={"date_trip_id"}),
 *        @ORM\Index(name="departure_time_index", columns={"departure_time"}),
 *        @ORM\Index(name="date_stoptime_import", columns={"date_trip_id", "date"})
 *	   },
 *     uniqueConstraints={
 *        @ORM\UniqueConstraint(name="date_trip_id_unique",columns={"date_trip_id"})
 *     }
 * )
 */
class StopTime implements IEntity
{
	use Identifier;

	/**
	 * @ORM\ManyToOne(targetEntity="App\Transport\Prague\Stop\Stop")
	 */
	private Stop $stop;

	/**
	 * @ORM\Column(type="datetime_immutable")
	 */
	private DateTimeImmutable $arrivalTime;

	/**
	 * @ORM\Column(type="datetime_immutable")
	 */
	private DateTimeImmutable $departureTime;

	/**
	 * @ORM\Column(type="date_immutable")
	 */
	private DateTimeImmutable $date;

	/**
	 * @ORM\Column(type="string")
	 */
	private string $tripId;

	/**
	 * @ORM\Column(type="string")
	 */
	private string $dateTripId;

	/**
	 * @ORM\Column(type="integer")
	 */
	private int $stopSequence;

	public function __construct(
		Stop $stop,
		DateTimeImmutable $arrivalTime,
		DateTimeImmutable $departureTime,
		DateTimeImmutable $date,
		string $tripId,
		int $stopSequence
	) {
		$this->stop = $stop;
		$this->arrivalTime = $arrivalTime;
		$this->departureTime = $departureTime;
		$this->date = $date;
		$this->tripId = $tripId;
		$this->stopSequence = $stopSequence;

		$this->dateTripId = $date->getTimestamp() . '_' . $tripId;
	}

	public function updateStopTime(
		DateTimeImmutable $arrivalTime,
		DateTimeImmutable $departureTime,
		int $stopSequence
	): void {
		$this->arrivalTime = $arrivalTime;
		$this->departureTime = $departureTime;
		$this->stopSequence = $stopSequence;
	}

	public function getStop(): Stop
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

	public function getStopSequence(): int
	{
		return $this->stopSequence;
	}

	public function getDate(): DateTimeImmutable
	{
		return $this->date;
	}

	public function getDateTripId(): string
	{
		return $this->dateTripId;
	}
}
