<?php

declare(strict_types=1);

namespace App\UI\Front\Prague\PragueDepartureTable\Control\PragueDepartureTableList;

use App\Transport\Prague\DepartureTable\DepartureTable;

class PragueDepartureTableListData
{
	public const PLACEHOLDER = '----';

	private DepartureTable $departureTable;

	private ?string $destinations = null;

	/** @var string[] */
	private array $lines;

	private ?string $lastUpdateTime;

	public function __construct(
		DepartureTable $departureTable,
		?string $destinations,
		?string $lines,
		?string $lastUpdateTime
	) {
		$this->departureTable = $departureTable;
		$this->destinations = $destinations !== null ? str_replace('~', ' - ', $destinations) : null;
		$this->lines = $lines !== null ? explode('~', $lines) : [];
		$this->lastUpdateTime = $lastUpdateTime;
	}

	public function getDepartureTable(): DepartureTable
	{
		return $this->departureTable;
	}

	public function getDestinations(): string
	{
		if ($this->destinations === null) {
			return self::PLACEHOLDER;
		}

		return $this->destinations;
	}

	public function getLastUpdateTime(): ?string
	{
		return $this->lastUpdateTime;
	}

	/**
	 * @return string[]
	 */
	public function getLines(): array
	{
		return $this->lines;
	}
}
