<?php

declare(strict_types=1);

namespace App\UI\Admin\Control\Statistic;

use App\Request\RequestRepository;
use App\Transport\Prague\Parking\ParkingLotOccupancyRepository;
use App\Transport\Prague\Parking\ParkingLotRepository;
use App\Transport\Prague\Statistic\TripList\TripListRepository;
use App\Transport\Prague\Stop\StopRepository;
use App\Transport\Prague\Vehicle\VehiclePositionRepository;
use App\UI\Shared\Statistic\Statistic;
use Mistrfilda\Datetime\DatetimeFactory;
use Nette\Application\UI\Control;

class StatisticControl extends Control
{
	private VehiclePositionRepository $vehiclePositionRepository;

	private StopRepository $stopRepository;

	private TripListRepository $tripListRepository;

	private ParkingLotRepository $parkingLotRepository;

	private ParkingLotOccupancyRepository $parkingLotOccupancyRepository;

	private RequestRepository $requestRepository;

	private string $template = __DIR__ . '/StatisticControl.latte';

	public function __construct(
		VehiclePositionRepository $VehiclePositionRepository,
		StopRepository $stopRepository,
		TripListRepository $tripListRepository,
		ParkingLotRepository $parkingLotRepository,
		ParkingLotOccupancyRepository $parkingLotOccupancyRepository,
		RequestRepository $requestRepository
	) {
		$this->vehiclePositionRepository = $VehiclePositionRepository;
		$this->stopRepository = $stopRepository;
		$this->tripListRepository = $tripListRepository;
		$this->parkingLotRepository = $parkingLotRepository;
		$this->parkingLotOccupancyRepository = $parkingLotOccupancyRepository;
		$this->requestRepository = $requestRepository;
	}

	public function setFrontTemplate(): void
	{
		$this->template = __DIR__ . '/StatisticControlFront.latte';
	}

	public function render(): void
	{
		$this->getTemplate()->statistics = $this->buildStatistics();
		$this->getTemplate()->setFile($this->template);
		$this->getTemplate()->render();
	}

	/**
	 * @return Statistic[]
	 */
	private function buildStatistics(): array
	{
		$statistics = [];
		$lastVehiclePosition = $this->vehiclePositionRepository->findLast();

		$statistics[] = new Statistic(
			Statistic::CONTEXTUAL_INFO,
			'Poslední aktualizace jízdních řádů',
			(string) $this->requestRepository->getLastRandomDepartureTableDownloadTime(),
			'fas fa-clock fa-2x text-info',
			'border-left-',
			'col-xl-12 col-md-12'
		);

		if ($lastVehiclePosition !== null) {
			$statistics[] = new Statistic(
				Statistic::CONTEXTUAL_SUCCESS,
				'Poslední známa poloha vozidel',
				$lastVehiclePosition->getCreatedAt()->format(DatetimeFactory::DEFAULT_DATETIME_FORMAT),
				'fas fa-clock fa-2x text-success',
				'border-left-',
				'col-xl-12 col-md-12'
			);

			$statistics[] = new Statistic(
				Statistic::CONTEXTUAL_SUCCESS,
				'Poslední počet poloh vozidel',
				(string) $lastVehiclePosition->getVehiclesCount(),
				'fas fa-bus fa-2x text-success'
			);
		}

		$statistics[] = new Statistic(
			Statistic::CONTEXTUAL_PRIMARY,
			'Celkový počet zastávek',
			(string) $this->stopRepository->getStopsCount(),
			'fas fa-ruler-vertical fa-2x text-primary'
		);

		$statistics[] = new Statistic(
			Statistic::CONTEXTUAL_PRIMARY,
			'Počet linek se statistikami',
			(string) $this->tripListRepository->getTripListLineCount(),
			'fas fa-database fa-2x text-primary',
			'border-left-',
			'col-xl-12 col-md-12'
		);

		$statistics[] = new Statistic(
			Statistic::CONTEXTUAL_PRIMARY,
			'Celkový počet statistik pro jednotlivá pořadí linek',
			(string) $this->tripListRepository->getTripListCount(),
			'fas fa-database fa-2x text-primary',
			'border-left-',
			'col-xl-12 col-md-12'
		);

		$statistics[] = new Statistic(
			Statistic::CONTEXTUAL_WARNING,
			'Počet dostupných parkovišť',
			(string) $this->parkingLotRepository->getParkingLotsCount(),
			'fas fa-parking fa-2x text-warning',
			'border-left-'
		);

		$statistics[] = new Statistic(
			Statistic::CONTEXTUAL_WARNING,
			'Data o parkovištích dostupná z',
			$this->parkingLotOccupancyRepository->getLastParkingDate(),
			'fas fa-parking fa-2x text-warning',
			'border-left-'
		);

		return $statistics;
	}
}
