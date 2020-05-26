<?php

declare(strict_types=1);

namespace App\UI\Shared\PragueDepartureTable\Control;

use App\Transport\Prague\DepartureTable\DepartureTableRepository;
use App\Transport\Prague\StopLine\StopTime\StopTimeRepository;
use App\Utils\DatetimeFactory;

class PragueDepartureTableListDataFactory
{
	/** @var DepartureTableRepository */
	private $departureTableRepository;

	/** @var StopTimeRepository */
	private $stopTimeRepository;

	/** @var DatetimeFactory */
	private $datetimeFactory;

	public function __construct(
		DepartureTableRepository $departureTableRepository,
		StopTimeRepository $stopTimeRepository,
		DatetimeFactory $datetimeFactory
	) {
		$this->departureTableRepository = $departureTableRepository;
		$this->stopTimeRepository = $stopTimeRepository;
		$this->datetimeFactory = $datetimeFactory;
	}

	/**
	 * @return PragueDepartureTableListData[]
	 */
	public function getAllDepartureTables(): array
	{
		$now = $this->datetimeFactory->createNow();

		$departureTables = $this->departureTableRepository->findAll();
		$allDestinations = $this->stopTimeRepository->findDepartureTablesDestinations(
			$now,
			$now->modify('+ 1 day')
		);

		$data = [];

		foreach ($departureTables as $departureTable) {
			$departureTableDestinations = null;

			if (array_key_exists($departureTable->getPragueStop()->getId(), $allDestinations)) {
				$departureTableDestinations = $allDestinations[$departureTable->getPragueStop()->getId()];
			}

			$data[] = new PragueDepartureTableListData(
				$departureTable,
				$departureTableDestinations
			);
		}

		return $data;
	}
}
