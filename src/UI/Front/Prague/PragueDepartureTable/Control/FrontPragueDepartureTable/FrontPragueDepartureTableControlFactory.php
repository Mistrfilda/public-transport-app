<?php

declare(strict_types=1);

namespace App\UI\Front\Prague\PragueDepartureTable\Control\FrontPragueDepartureTable;

interface FrontPragueDepartureTableControlFactory
{
	public function create(string $id): FrontPragueDepartureTableControl;
}
