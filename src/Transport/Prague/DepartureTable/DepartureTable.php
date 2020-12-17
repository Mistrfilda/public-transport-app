<?php

declare(strict_types=1);

namespace App\Transport\Prague\DepartureTable;

use App\Doctrine\CreatedAt;
use App\Doctrine\IEntity;
use App\Doctrine\UpdatedAt;
use App\Doctrine\Uuid;
use App\Transport\DepartureTable\IDepartureTable;
use App\Transport\Prague\Stop\Stop;
use App\Transport\Stop\IStop;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Mistrfilda\Datetime\Types\DatetimeImmutable;

/**
 * @ORM\Entity
 * @ORM\Table(name="prague_departure_table")
 */
class DepartureTable implements IDepartureTable, IEntity, JsonSerializable
{
	use Uuid;
	use CreatedAt;
	use UpdatedAt;

	/**
	 * @ORM\ManyToOne(targetEntity="App\Transport\Prague\Stop\Stop")
	 */
	private Stop $stop;

	/**
	 * @ORM\Column(type="integer")
	 */
	private int $numberOfFutureDays;

	public function __construct(
		Stop $stop,
		int $numberOfFutureDays,
		DateTimeImmutable $now
	) {
		$this->stop = $stop;
		$this->numberOfFutureDays = $numberOfFutureDays;
		$this->createdAt = $now;
		$this->updatedAt = $now;
	}

	public function update(
		int $numberOfFutureDays,
		DateTimeImmutable $now
	): void {
		$this->numberOfFutureDays = $numberOfFutureDays;
		$this->updatedAt = $now;
	}

	public function getStop(): IStop
	{
		return $this->stop;
	}

	public function getDownloadNumberOfDays(): int
	{
		return $this->numberOfFutureDays;
	}

	public function getNumberOfFutureDays(): int
	{
		return $this->numberOfFutureDays;
	}

	public function getPragueStop(): Stop
	{
		return $this->stop;
	}

	public function getAdminFormatedName(): string
	{
		return sprintf('%s (%s)', $this->getPragueStop()->getName(), $this->getId()->toString());
	}

	/**
	 * @return array<string, string|int>
	 */
	public function jsonSerialize(): array
	{
		return [
			'id' => $this->getId()->toString(),
		];
	}
}
