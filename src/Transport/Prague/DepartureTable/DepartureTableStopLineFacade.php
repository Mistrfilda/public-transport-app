<?php

declare(strict_types=1);

namespace App\Transport\Prague\DepartureTable;

use App\Transport\Prague\StopLine\StopTime\Import\StopTimeImportFacade;
use App\Transport\Prague\StopLine\Trip\Import\TripImportFacade;
use Ramsey\Uuid\UuidInterface;

class DepartureTableStopLineFacade
{
	private DepartureTableRepository $departureTableRepository;

	private StopTimeImportFacade $stopTimeImportFacade;

	private TripImportFacade $tripImportFacade;

	public function __construct(
		DepartureTableRepository $departureTableRepository,
		StopTimeImportFacade $stopTimeImportFacade,
		TripImportFacade $tripImportFacade
	) {
		$this->departureTableRepository = $departureTableRepository;
		$this->stopTimeImportFacade = $stopTimeImportFacade;
		$this->tripImportFacade = $tripImportFacade;
	}

	public function downloadStoplinesForDepartureTable(UuidInterface $departureTableId): void
	{
		$departureTable = $this->departureTableRepository->findById($departureTableId);

		$this->stopTimeImportFacade->import(
			$departureTable->getPragueStop()->getId(),
			$departureTable->getNumberOfFutureDays()
		);

		$this->tripImportFacade->import(
			$departureTable->getPragueStop()->getId(),
			$departureTable->getNumberOfFutureDays()
		);
	}
}
