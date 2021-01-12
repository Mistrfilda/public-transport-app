<?php

declare(strict_types=1);

namespace App\UI\Front\Homepage;

use App\Transport\Prague\Vehicle\VehicleMapObjectProvider;
use App\Transport\TransportRestriction\TransportRestrictionType;
use App\UI\Front\FrontPresenter;
use App\UI\Front\Prague\PragueDepartureTable\Control\PragueDepartureTableList\PragueDepartureTableListControl;
use App\UI\Front\Prague\PragueDepartureTable\Control\PragueDepartureTableList\PragueDepartureTableListControlFactory;
use App\UI\Front\Prague\PragueTransportRestriction\Control\Modal\PragueRestrictionModalControl;
use App\UI\Front\Prague\PragueTransportRestriction\Control\Modal\PragueRestrictionModalControlFactory;
use App\UI\Front\Prague\PragueTransportRestriction\Control\PragueTransportRestrictionControl;
use App\UI\Front\Prague\PragueTransportRestriction\Control\PragueTransportRestrictionControlFactory;
use App\UI\Front\Statistic\Control\System\SystemStatisticControl;
use App\UI\Front\Statistic\Control\System\SystemStatisticControlFactory;
use App\UI\Shared\Map\MapControl;
use App\UI\Shared\Map\MapControlFactory;
use Ramsey\Uuid\Uuid;

class HomepagePresenter extends FrontPresenter
{
	private MapControlFactory $mapControlFactory;

	private VehicleMapObjectProvider $vehicleMapObjectProvider;

	private PragueDepartureTableListControlFactory $pragueDepartureTableListControlFactory;

	private PragueTransportRestrictionControlFactory $pragueTransportRestrictionControlFactory;

	private SystemStatisticControlFactory $systemStatisticControlFactory;

	private PragueRestrictionModalControlFactory $pragueRestrictionModalControlFactory;

	public function __construct(
		MapControlFactory $mapControlFactory,
		VehicleMapObjectProvider $vehicleMapObjectProvider,
		PragueDepartureTableListControlFactory $pragueDepartureTableListControlFactory,
		PragueTransportRestrictionControlFactory $pragueTransportRestrictionControlFactory,
		SystemStatisticControlFactory $systemStatisticControlFactory,
		PragueRestrictionModalControlFactory $pragueRestrictionModalControlFactory
	) {
		parent::__construct();
		$this->mapControlFactory = $mapControlFactory;
		$this->vehicleMapObjectProvider = $vehicleMapObjectProvider;
		$this->pragueDepartureTableListControlFactory = $pragueDepartureTableListControlFactory;
		$this->pragueTransportRestrictionControlFactory = $pragueTransportRestrictionControlFactory;
		$this->systemStatisticControlFactory = $systemStatisticControlFactory;
		$this->pragueRestrictionModalControlFactory = $pragueRestrictionModalControlFactory;
	}

	public function renderDefault(): void
	{
	}

	public function handleShowTransportRestrictionModal(string $transportRestrictionId): void
	{
		$this['transportRestrictionModal']->setTransportRestrictionId(
			Uuid::fromString($transportRestrictionId)
		);
		$this->showModal(
			'transportRestrictionModal'
		);
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
		return $control;
	}

	protected function createComponentTransportRestrictionModal(): PragueRestrictionModalControl
	{
		return $this->pragueRestrictionModalControlFactory->create();
	}
}
