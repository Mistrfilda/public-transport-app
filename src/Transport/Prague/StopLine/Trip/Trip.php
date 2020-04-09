<?php

declare(strict_types=1);

namespace App\Transport\Prague\StopLine\Trip;

use App\Doctrine\Identifier;
use App\Doctrine\IEntity;
use App\Transport\Prague\Stop\Stop;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="prague_trip")
 */
class Trip implements IEntity
{
	use Identifier;

	/**
	 * @var Stop
	 * @ORM\ManyToOne(targetEntity="App\Transport\Prague\Stop\Stop")
	 */
	private $stop;

	/**
	 * @var string
	 * @ORM\Column(type="string")
	 */
	private $serviceId;

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
	 * @var string
	 * @ORM\Column(type="string")
	 */
	private $tripHeadsign;

	/**
	 * @var bool
	 * @ORM\Column(type="string")
	 */
	private $wheelchairAccessible;

	/**
	 * @var DateTimeImmutable
	 * @ORM\Column(type="date_immutable")
	 */
	private $date;

	/**
	 * @var string
	 * @ORM\Column(type="string")
	 */
	private $lineNumber;

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
