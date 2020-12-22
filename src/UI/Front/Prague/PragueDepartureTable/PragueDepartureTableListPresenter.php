<?php

declare(strict_types=1);

namespace App\UI\Front\Prague\PragueDepartureTable;

use App\UI\Front\FrontPresenter;
use App\UI\Front\Prague\PragueDepartureTable\Control\PragueDepartureTableList\PragueDepartureTableListControl;
use App\UI\Front\Prague\PragueDepartureTable\Control\PragueDepartureTableList\PragueDepartureTableListControlFactory;

class PragueDepartureTableListPresenter extends FrontPresenter
{
	private PragueDepartureTableListControlFactory $pragueDepartureTableListControlFactory;

	public function __construct(PragueDepartureTableListControlFactory $pragueDepartureTableListControlFactory)
	{
		parent::__construct();
		$this->pragueDepartureTableListControlFactory = $pragueDepartureTableListControlFactory;
	}

	protected function createComponentPragueDepartureTableListControl(): PragueDepartureTableListControl
	{
		return $this->pragueDepartureTableListControlFactory->create();
	}
}
