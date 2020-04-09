<?php

declare(strict_types=1);

namespace App\UI\Admin\PragueStatistic;

use App\UI\Admin\AdminPresenter;
use App\UI\Admin\Base\AdminDatagrid;
use App\UI\Admin\PragueStatistic\Datagrid\StatisticDatagridFactory;

class PragueStatisticPresenter extends AdminPresenter
{
	/** @var StatisticDatagridFactory */
	private $statisticDatagridFactory;

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
