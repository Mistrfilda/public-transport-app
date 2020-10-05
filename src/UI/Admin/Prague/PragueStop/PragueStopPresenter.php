<?php

declare(strict_types=1);

namespace App\UI\Admin\Prague\PragueStop;

use App\UI\Admin\AdminPresenter;
use App\UI\Admin\Base\AdminDatagrid;
use App\UI\Admin\Prague\PragueStop\Datagrid\StopDatagridFactory;

class PragueStopPresenter extends AdminPresenter
{
	private StopDatagridFactory $stopDatagridFactory;

	public function __construct(StopDatagridFactory $stopDatagridFactory)
	{
		parent::__construct();
		$this->stopDatagridFactory = $stopDatagridFactory;
	}

	protected function createComponentStopGrid(): AdminDatagrid
	{
		return $this->stopDatagridFactory->create();
	}
}
