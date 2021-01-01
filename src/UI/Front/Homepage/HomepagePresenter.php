<?php

declare(strict_types=1);

namespace App\UI\Front\Homepage;

use App\Transport\Prague\DepartureTable\DepartureTableRepository;
use App\Transport\Prague\Vehicle\VehicleMapObjectProvider;
use App\Transport\TransportRestriction\TransportRestrictionType;
use App\UI\Admin\Control\Statistic\StatisticControl;
use App\UI\Admin\Control\Statistic\StatisticControlFactory;
use App\UI\Front\FrontPresenter;
use App\UI\Front\Prague\PragueDepartureTable\Control\PragueDepartureTableList\PragueDepartureTableListControl;
use App\UI\Front\Prague\PragueDepartureTable\Control\PragueDepartureTableList\PragueDepartureTableListControlFactory;
use App\UI\Front\Prague\PragueTransportRestriction\Control\PragueTransportRestrictionControl;
use App\UI\Front\Prague\PragueTransportRestriction\Control\PragueTransportRestrictionControlFactory;
use App\UI\Shared\Map\MapControl;
use App\UI\Shared\Map\MapControlFactory;

class HomepagePresenter extends FrontPresenter
{
	private DepartureTableRepository $departureTableRepository;

	private StatisticControlFactory $statisticControlFactory;

	private MapControlFactory $mapControlFactory;

	private VehicleMapObjectProvider $vehicleMapObjectProvider;

	private PragueDepartureTableListControlFactory $pragueDepartureTableListControlFactory;

	private PragueTransportRestrictionControlFactory $pragueTransportRestrictionControlFactory;

	public function __construct(
		DepartureTableRepository $departureTableRepository,
		StatisticControlFactory $statisticControlFactory,
		MapControlFactory $mapControlFactory,
		VehicleMapObjectProvider $vehicleMapObjectProvider,
		PragueDepartureTableListControlFactory $pragueDepartureTableListControlFactory,
		PragueTransportRestrictionControlFactory $pragueTransportRestrictionControlFactory
	) {
		parent::__construct();
		$this->departureTableRepository = $departureTableRepository;
		$this->statisticControlFactory = $statisticControlFactory;
		$this->mapControlFactory = $mapControlFactory;
		$this->vehicleMapObjectProvider = $vehicleMapObjectProvider;
		$this->pragueDepartureTableListControlFactory = $pragueDepartureTableListControlFactory;
		$this->pragueTransportRestrictionControlFactory = $pragueTransportRestrictionControlFactory;
	}

	public function renderDefault(): void
	{
		$this->template->departureTables = $this->departureTableRepository->findAll();
	}

	protected function createComponentStatisticControl(): StatisticControl
	{
		$control = $this->statisticControlFactory->create();
		$control->setFrontTemplate();
		return $control;
	}

	protected function createComponentMapControl(): MapControl
	{
		return $this->mapControlFactory->create($this->vehicleMapObjectProvider);
	}

	protected function createComponentPragueDepartureTableListControl(): PragueDepartureTableListControl
	{
		$control = $this->pragueDepartureTableListControlFactory->create();
		$control->setAdditionalParameters('Vybrané odjezdové tabule', 2);
		return $control;
	}

	protected function createComponentShortTermRestrictionControl(): PragueTransportRestrictionControl
	{
		$control = $this->pragueTransportRestrictionControlFactory->create();
		$control->setRestrictionType(TransportRestrictionType::SHORT_TERM);
		$control->setCardGridColumn('col-md-12');
		return $control;
	}
}
