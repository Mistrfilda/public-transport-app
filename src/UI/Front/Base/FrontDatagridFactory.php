<?php

declare(strict_types=1);

namespace App\UI\Front\Base;

class FrontDatagridFactory
{
	public function create(): FrontDatagrid
	{
		$grid = new FrontDatagrid();

		$grid::$iconPrefix = 'fa fa-';

		//GLOBAL PRESETS
		$grid->setRememberState(false);
		$grid->setOuterFilterRendering(true);
		$grid->setAutoSubmit(false);

		$grid->setTemplateFile(__DIR__ . '/templates/frontDatagrid.latte');

		return $grid;
	}
}
