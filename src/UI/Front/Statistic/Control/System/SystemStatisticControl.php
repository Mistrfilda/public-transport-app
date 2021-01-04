<?php

declare(strict_types=1);

namespace App\UI\Front\Statistic\Control\System;

use App\Request\RequestRepository;
use App\Transport\Prague\Parking\ParkingLotOccupancyRepository;
use App\Transport\Prague\Parking\ParkingLotRepository;
use App\Transport\Prague\Statistic\TripList\TripListRepository;
use App\Transport\Prague\Stop\StopRepository;
use App\Transport\Prague\Vehicle\VehiclePositionRepository;
use App\UI\Front\Base\BaseControl;
use App\UI\Front\Statistic\FrontStatistic;
use Mistrfilda\Datetime\DatetimeFactory;

class SystemStatisticControl extends BaseControl
{
	private VehiclePositionRepository $vehiclePositionRepository;

	private StopRepository $stopRepository;

	private TripListRepository $tripListRepository;

	private ParkingLotRepository $parkingLotRepository;

	private ParkingLotOccupancyRepository $parkingLotOccupancyRepository;

	private RequestRepository $requestRepository;

	public function __construct(
		VehiclePositionRepository $vehiclePositionRepository,
		StopRepository $stopRepository,
		TripListRepository $tripListRepository,
		ParkingLotRepository $parkingLotRepository,
		ParkingLotOccupancyRepository $parkingLotOccupancyRepository,
		RequestRepository $requestRepository
	) {
		$this->vehiclePositionRepository = $vehiclePositionRepository;
		$this->stopRepository = $stopRepository;
		$this->tripListRepository = $tripListRepository;
		$this->parkingLotRepository = $parkingLotRepository;
		$this->parkingLotOccupancyRepository = $parkingLotOccupancyRepository;
		$this->requestRepository = $requestRepository;
	}

	public function render(): void
	{
		$template = $this->createTemplate(SystemStatisticControlTemplate::class);
		$template->statistics = $this->buildStatistics();
		$template->setFile(str_replace('.php', '.latte', __FILE__));
		$template->render();
	}

	/**
	 * @return FrontStatistic[]
	 */
	protected function buildStatistics(): array
	{
		$statistics = [];
		$lastVehiclePosition = $this->vehiclePositionRepository->findLast();

		$statistics[] = new FrontStatistic(
			'Poslední aktualizace jízdních řádů',
			(string) $this->requestRepository->getLastRandomDepartureTableDownloadTime(),
			'fas fa-clock fa-2x',
			FrontStatistic::GREEN
		);

		if ($lastVehiclePosition !== null) {
			$statistics[] = new FrontStatistic(
				'Poslední známa poloha vozidel',
				$lastVehiclePosition->getCreatedAt()->format(DatetimeFactory::DEFAULT_DATETIME_FORMAT),
				'fas fa-clock fa-2x',
				FrontStatistic::GREEN
			);

			$statistics[] = new FrontStatistic(
				'Poslední počet poloh vozidel',
				(string) $lastVehiclePosition->getVehiclesCount(),
				'fas fa-bus fa-2x',
				FrontStatistic::INDIGO
			);
		}

		$statistics[] = new FrontStatistic(
			'Celkový počet zastávek',
			(string) $this->stopRepository->getStopsCount(),
			'fas fa-ruler-vertical fa-2x text-primary',
			FrontStatistic::INDIGO
		);

		$statistics[] = new FrontStatistic(
			'Počet linek se statistikami',
			(string) $this->tripListRepository->getTripListLineCount(),
			'fas fa-database fa-2x',
			FrontStatistic::BLUE
		);

		$statistics[] = new FrontStatistic(
			'Celkový počet statistik pro jednotlivá pořadí linek',
			(string) $this->tripListRepository->getTripListCount(),
			'fas fa-database fa-2x',
			FrontStatistic::BLUE
		);

		$statistics[] = new FrontStatistic(
			'Počet dostupných parkovišť',
			(string) $this->parkingLotRepository->getParkingLotsCount(),
			'fas fa-parking fa-2x',
			FrontStatistic::LIGHT_BLUE
		);

		$statistics[] = new FrontStatistic(
			'Data o parkovištích dostupná z',
			$this->parkingLotOccupancyRepository->getLastParkingDate(),
			'fas fa-parking fa-2x',
			FrontStatistic::LIGHT_BLUE
		);

		return $statistics;
	}
}
