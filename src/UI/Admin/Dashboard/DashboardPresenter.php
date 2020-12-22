<?php

declare(strict_types=1);

namespace App\UI\Admin\Dashboard;

use App\UI\Admin\AdminPresenter;
use App\UI\Admin\Control\Statistic\StatisticControl;
use App\UI\Admin\Control\Statistic\StatisticControlFactory;

class DashboardPresenter extends AdminPresenter
{
	private StatisticControlFactory $statisticControlFactory;

	public function __construct(
		StatisticControlFactory $statisticControlFactory
	) {
		parent::__construct();
		$this->statisticControlFactory = $statisticControlFactory;
	}

	protected function createComponentStatisticControl(): StatisticControl
	{
		return $this->statisticControlFactory->create();
	}
}
