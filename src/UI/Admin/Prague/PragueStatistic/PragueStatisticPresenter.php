<?php

declare(strict_types=1);

namespace App\UI\Admin\Prague\PragueStatistic;

use App\UI\Admin\AdminPresenter;
use App\UI\Admin\Base\AdminDatagrid;
use App\UI\Admin\Prague\PragueStatistic\Datagrid\StatisticDatagridFactory;

class PragueStatisticPresenter extends AdminPresenter
{
	private StatisticDatagridFactory $statisticDatagridFactory;

	public function __construct(StatisticDatagridFactory $statisticDatagridFactory)
	{
		parent::__construct();
		$this->statisticDatagridFactory = $statisticDatagridFactory;
	}

	protected function createComponentStatisticDatagrid(): AdminDatagrid
	{
		return $this->statisticDatagridFactory->create();
	}
}
