<?php

declare(strict_types=1);

namespace App\UI\Shared\PragueDepartureTable\Control;

use Nette\Application\UI\Control;

class PragueDepartureTableListControl extends Control
{
	private PragueDepartureTableListDataFactory $departureTableListDataFactory;

	public function __construct(
		PragueDepartureTableListDataFactory $departureTableListDataFactory
	) {
		$this->departureTableListDataFactory = $departureTableListDataFactory;
	}

	public function render(): void
	{
		$this->getTemplate()->departureTableData = $this->departureTableListDataFactory->getAllDepartureTables();
		$this->getTemplate()->setFile(str_replace('.php', '.latte', __FILE__));
		$this->getTemplate()->render();
	}
}
