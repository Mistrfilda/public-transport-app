<?php

declare(strict_types=1);

namespace App\Transport\Prague\StopLine\StopTime;

use App\Doctrine\Identifier;
use App\Doctrine\IEntity;
use App\Transport\Prague\Stop\Stop;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="prague_stop_time",
 *     indexes={
 *        @ORM\Index(name="date_trip_id_index", columns={"date_trip_id"})
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
	 * @var Stop
	 * @ORM\ManyToOne(targetEntity="App\Transport\Prague\Stop\Stop")
	 */
	private $stop;

	/**
	 * @var DateTimeImmutable
	 * @ORM\Column(type="datetime_immutable")
	 */
	private $arrivalTime;

	/**
	 * @var DateTimeImmutable
	 * @ORM\Column(type="datetime_immutable")
	 */
	private $departureTime;

	/**
	 * @var DateTimeImmutable
	 * @ORM\Column(type="date_immutable")
	 */
	private $date;

	/**
	 * @var string
	 * @ORM\Column(type="string")
	 */
	private $tripId;

	/**
	 * @var string
	 * @ORM\Column(type="string")
	 */
	private $dateTripId;

	/**
	 * @var int
	 * @ORM\Column(type="integer")
	 */
	private $stopSequence;

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
