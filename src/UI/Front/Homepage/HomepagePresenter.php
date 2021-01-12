<?php

declare(strict_types=1);

namespace App\UI\Front\Homepage;

use App\Transport\Prague\Vehicle\VehicleMapObjectProvider;
use App\Transport\TransportRestriction\TransportRestrictionType;
use App\UI\Front\FrontPresenter;
use App\UI\Front\Prague\PragueDepartureTable\Control\PragueDepartureTableList\PragueDepartureTableListControl;
use App\UI\Front\Prague\PragueDepartureTable\Control\PragueDepartureTableList\PragueDepartureTableListControlFactory;
use App\UI\Front\Prague\PragueTransportRestriction\Control\PragueTransportRestrictionControl;
use App\UI\Front\Prague\PragueTransportRestriction\Control\PragueTransportRestrictionControlFactory;
use App\UI\Front\Statistic\Control\System\SystemStatisticControl;
use App\UI\Front\Statistic\Control\System\SystemStatisticControlFactory;
use App\UI\Shared\Map\MapControl;
use App\UI\Shared\Map\MapControlFactory;

class HomepagePresenter extends FrontPresenter
{
	private MapControlFactory $mapControlFactory;

	private VehicleMapObjectProvider $vehicleMapObjectProvider;

	private PragueDepartureTableListControlFactory $pragueDepartureTableListControlFactory;

	private PragueTransportRestrictionControlFactory $pragueTransportRestrictionControlFactory;

	private SystemStatisticControlFactory $systemStatisticControlFactory;

	public function __construct(
		MapControlFactory $mapControlFactory,
		VehicleMapObjectProvider $vehicleMapObjectProvider,
		PragueDepartureTableListControlFactory $pragueDepartureTableListControlFactory,
		PragueTransportRestrictionControlFactory $pragueTransportRestrictionControlFactory,
		SystemStatisticControlFactory $systemStatisticControlFactory
	) {
		parent::__construct();
		$this->mapControlFactory = $mapControlFactory;
		$this->vehicleMapObjectProvider = $vehicleMapObjectProvider;
		$this->pragueDepartureTableListControlFactory = $pragueDepartureTableListControlFactory;
		$this->pragueTransportRestrictionControlFactory = $pragueTransportRestrictionControlFactory;
		$this->systemStatisticControlFactory = $systemStatisticControlFactory;
	}

	public function renderDefault(): void
	{
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

	protected function createComponentSystemStatisticControl(): SystemStatisticControl
	{
		return $this->systemStatisticControlFactory->create();
	}

	protected function createComponentShortTermRestrictionControl(): PragueTransportRestrictionControl
	{
		$control = $this->pragueTransportRestrictionControlFactory->create();
		$control->setRestrictionType(TransportRestrictionType::SHORT_TERM);
		$control->setCardGridColumn('col-md-12');
		return $control;
	}
}
