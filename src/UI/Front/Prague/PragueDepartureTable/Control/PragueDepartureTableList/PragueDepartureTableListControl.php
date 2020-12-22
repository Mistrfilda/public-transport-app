<?php

declare(strict_types=1);

namespace App\UI\Front\Prague\PragueDepartureTable\Control\PragueDepartureTableList;

use Nette\Application\UI\Control;

class PragueDepartureTableListControl extends Control
{
	private PragueDepartureTableListDataFactory $departureTableListDataFactory;

	private string $heading = 'Odjezdové tabule';

	private ?int $count = null;

	public function __construct(
		PragueDepartureTableListDataFactory $departureTableListDataFactory
	) {
		$this->departureTableListDataFactory = $departureTableListDataFactory;
	}

	public function setAdditionalParameters(string $heading, ?int $count): void
	{
		$this->heading = $heading;
		$this->count = $count;
	}

	public function render(): void
	{
		$this->getTemplate()->heading = $this->heading;
		$this->getTemplate()->departureTableData = $this->departureTableListDataFactory->getAllDepartureTables($this->count);
		$this->getTemplate()->setFile(str_replace('.php', '.latte', __FILE__));
		$this->getTemplate()->render();
	}
}
