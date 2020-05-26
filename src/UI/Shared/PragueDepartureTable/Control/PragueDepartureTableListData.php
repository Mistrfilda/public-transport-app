<?php

declare(strict_types=1);

namespace App\UI\Shared\PragueDepartureTable\Control;

use App\Transport\Prague\DepartureTable\DepartureTable;

class PragueDepartureTableListData
{
	public const PLACEHOLDER = '----';

	/** @var DepartureTable */
	private $departureTable;

	/** @var string|null */
	private $destinations;

	public function __construct(DepartureTable $departureTable, ?string $destinations)
	{
		$this->departureTable = $departureTable;
		$this->destinations = $destinations !== null ? str_replace('~', ' - ', $destinations) : null;
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
}
