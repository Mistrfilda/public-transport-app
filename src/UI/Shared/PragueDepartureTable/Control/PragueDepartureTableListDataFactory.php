<?php

declare(strict_types=1);

namespace App\UI\Shared\PragueDepartureTable\Control;

use App\Transport\Prague\DepartureTable\DepartureTableRepository;
use App\Transport\Prague\StopLine\StopTime\StopTimeRepository;
use App\Utils\Datetime\DatetimeFactory;

class PragueDepartureTableListDataFactory
{
	private DepartureTableRepository $departureTableRepository;

	private StopTimeRepository $stopTimeRepository;

	private DatetimeFactory $datetimeFactory;

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
	public function getAllDepartureTables(?int $count = null): array
	{
		$now = $this->datetimeFactory->createNow();

		$departureTables = $this->departureTableRepository->findAll();
		$allDestinations = $this->stopTimeRepository->findDepartureTablesDestinations(
			$now,
			$now->modify('+ 3 day')
		);

		$allLines = $this->stopTimeRepository->findDepartureTablesLines(
			$now,
			$now->modify('+ 3 day')
		);

		$data = [];
		$iterator = 0;

		foreach ($departureTables as $departureTable) {
			$departureTableDestinations = null;
			$lines = null;

			if ($count !== null && $iterator >= $count) {
				break;
			}

			if (array_key_exists($departureTable->getPragueStop()->getId(), $allDestinations)) {
				$departureTableDestinations = $allDestinations[$departureTable->getPragueStop()->getId()];
			}

			if (array_key_exists($departureTable->getPragueStop()->getId(), $allLines)) {
				$lines = $allLines[$departureTable->getPragueStop()->getId()];
			}

			$data[] = new PragueDepartureTableListData(
				$departureTable,
				$departureTableDestinations,
				$lines
			);

			$iterator++;
		}

		return $data;
	}
}
