<?php

declare(strict_types=1);

namespace App\Request;

use App\Doctrine\CreatedAt;
use App\Doctrine\Identifier;
use App\Doctrine\IEntity;
use App\Transport\Prague\DepartureTable\DepartureTable;
use Doctrine\ORM\Mapping as ORM;
use Mistrfilda\Datetime\Types\DatetimeImmutable;

/**
 * @ORM\Entity
 * @ORM\Table(name="request")
 */
class Request implements IEntity
{
	use Identifier;
	use CreatedAt;

	/**
	 * @ORM\Column(type="string")
	 */
	private string $type;

	/**
	 * @ORM\Column(type="datetime_immutable", nullable=true)
	 */
	private ?DateTimeImmutable $finishedAt = null;

	/**
	 * @ORM\Column(type="datetime_immutable", nullable=true)
	 */
	private ?DateTimeImmutable $failedAt = null;

	/**
	 * @ORM\Column(type="datetime_immutable", nullable=true)
	 */
	private ?DateTimeImmutable $requeuedAt = null;

	/**
	 * @ORM\ManyToOne(targetEntity="App\Transport\Prague\DepartureTable\DepartureTable")
	 * @ORM\JoinColumn(onDelete="set null")
	 */
	private ?DepartureTable $pragueDepartureTable = null;

	public function __construct(
		string $type,
		DateTimeImmutable $now,
		?DepartureTable $pragueDepartureTable = null
	) {
		RequestType::validate($type);
		$this->type = $type;
		$this->createdAt = $now;
		$this->pragueDepartureTable = $pragueDepartureTable;
	}

	public function finished(DateTimeImmutable $now): void
	{
		$this->finishedAt = $now;
	}

	public function failed(DateTimeImmutable $now): void
	{
		$this->failedAt = $now;
	}

	public function getType(): string
	{
		return $this->type;
	}

	public function getFinishedAt(): ?DateTimeImmutable
	{
		return $this->finishedAt;
	}

	public function getFailedAt(): ?DateTimeImmutable
	{
		return $this->failedAt;
	}

	public function getRequeuedAt(): ?DateTimeImmutable
	{
		return $this->requeuedAt;
	}

	public function getPragueDepartureTable(): DepartureTable
	{
		if ($this->pragueDepartureTable === null) {
			throw new RequestException('Prague departure table is not set for this request');
		}

		return $this->pragueDepartureTable;
	}

	public function hasPragueDepartureTable(): bool
	{
		return $this->pragueDepartureTable !== null;
	}

	public function hasFailed(): bool
	{
		return $this->failedAt !== null;
	}

	public function hasFinished(): bool
	{
		return $this->finishedAt !== null;
	}
}
