<?php

declare(strict_types=1);

namespace App\UI\Front\Statistic\Control\Main;

use App\UI\Front\Base\BaseControl;
use App\UI\Front\Base\FrontDatagrid;
use App\UI\Front\Statistic\Datagrid\Trip\RouteStatisticDatagridFactory;

class MainStatisticControl extends BaseControl
{
	/** @var RouteStatisticDatagridFactory */
	private $routeStatisticDatagridFactory;

	public function __construct(RouteStatisticDatagridFactory $routeStatisticDatagridFactory)
	{
		$this->routeStatisticDatagridFactory = $routeStatisticDatagridFactory;
	}

	public function render(): void
	{
		$template = $this->getTemplate();
		$template->setFile(str_replace('.php', '.latte', __FILE__));
		$template->render();
	}

	protected function createComponentRouteStatisticDatagrid(): FrontDatagrid
	{
		return $this->routeStatisticDatagridFactory->create();
	}
}
