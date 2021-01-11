<?php

declare(strict_types=1);

namespace App\UI\Front\Statistic;

use App\UI\Front\Control\Datagrid\FrontDatagrid;
use App\UI\Front\FrontPresenter;
use App\UI\Front\Statistic\Control\System\SystemStatisticControl;
use App\UI\Front\Statistic\Control\System\SystemStatisticControlFactory;
use App\UI\Front\Statistic\Datagrid\Trip\TripListDatagridFactory;

class StatisticPresenter extends FrontPresenter
{
	private SystemStatisticControlFactory $systemStatisticControlFactory;

	private TripListDatagridFactory $tripListDatagridFactory;

	public function __construct(
		SystemStatisticControlFactory $systemStatisticControlFactory,
		TripListDatagridFactory $tripListDatagridFactory
	) {
		parent::__construct();
		$this->systemStatisticControlFactory = $systemStatisticControlFactory;
		$this->tripListDatagridFactory = $tripListDatagridFactory;
	}

	protected function createComponentStatisticControl(): SystemStatisticControl
	{
		return $this->systemStatisticControlFactory->create();
	}

	protected function createComponentTripListDatagrid(): FrontDatagrid
	{
		return $this->tripListDatagridFactory->create();
	}
}
