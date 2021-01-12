<?php

declare(strict_types=1);

namespace App\UI\Front\Prague\PragueDepartureTable;

use App\Transport\Prague\DepartureTable\DepartureTable;
use App\UI\Front\Base\BasePresenterTemplate;
use Nette\SmartObject;

class PragueDepartureTableTemplate extends BasePresenterTemplate
{
	use SmartObject;

	public PragueDepartureTablePresenter $presenter;

	public DepartureTable $departureTable;
}
