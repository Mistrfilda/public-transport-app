<?php

declare(strict_types=1);

namespace App\UI\Front\Statistic\Datagrid\Trip;

use App\Transport\Prague\Statistic\TripStatisticData;
use App\Transport\Prague\Statistic\TripStatisticDataRepository;
use App\UI\Front\Base\FrontDatagrid;
use App\UI\Front\Base\FrontDatagridFactory;

class TripStatisticDataDatagridFactory
{
	private FrontDatagridFactory $frontDatagridFactory;

	private TripStatisticDataRepository $tripStatisticDataRepository;

	public function __construct(
		FrontDatagridFactory $frontDatagridFactory,
		TripStatisticDataRepository $tripStatisticDataRepository
	) {
		$this->frontDatagridFactory = $frontDatagridFactory;
		$this->tripStatisticDataRepository = $tripStatisticDataRepository;
	}

	public function create(string $tripId): FrontDatagrid
	{
		$grid = $this->frontDatagridFactory->create();

		$qb = $this->tripStatisticDataRepository->createQueryBuilder();
		$qb->andWhere($qb->expr()->eq('tripStatistic.tripId', ':tripId'));
		$qb->setParameter('tripId', $tripId);
		$grid->setDataSource($qb);

		$grid->addColumnDate('date', 'Datum')->setSortable()->setFilterDate();
		$grid->addColumnText('dayName', 'Nazev dne');
		$czechHoliday = $grid->addColumnText('isCzechHoliday', 'Státní svátek')->setRenderer(
			function (TripStatisticData $tripStatisticData) {
				if ($tripStatisticData->isCzechHoliday()) {
					return 'Ano';
				}

				return 'Ne';
			}
		);

		$grid->setFilterSelect($czechHoliday, [
			0 => 'Ne',
			1 => 'Ano',
		]);

		$grid->addColumnDateTime('oldestKnownPosition', 'První známá poloha');
		$grid->addColumnDateTime('newestKnownPosition', 'Poslední známá poloha');

		$grid->addColumnText('routeId', 'Route ID')->setFilterText();
		$grid->addColumnText('company', 'Společnost')->setFilterText();

		$grid->addColumnText('vehicleId', 'Vozidlo')
			->setRenderer(function (TripStatisticData $tripStatisticData): string {
				if ($tripStatisticData->getVehicleId() !== null) {
					return $tripStatisticData->getVehicleId();
				}

				return FrontDatagrid::NULLABLE_PLACEHOLDER;
			})->setFilterText();
		$grid->addColumnText('finalStation', 'Konečná stanice')->setFilterText();

		$grid->addColumnText('averageDelay', 'Průměrné zpoždění')->setSortable();
		$grid->addColumnText('highestDelay', 'Nejvyšší zpoždění')->setSortable();

		$grid->addColumnText('lastPositionDelay', 'Zpoždění v cílové zastávce')
			->setRenderer(function (TripStatisticData $tripStatisticData): string {
				if ($tripStatisticData->getLastPositionDelay() !== null) {
					return (string) $tripStatisticData->getLastPositionDelay();
				}

				return FrontDatagrid::NULLABLE_PLACEHOLDER;
			})->setSortable();

		$grid->setDefaultSort(['date' => 'desc']);

		return $grid;
	}
}
