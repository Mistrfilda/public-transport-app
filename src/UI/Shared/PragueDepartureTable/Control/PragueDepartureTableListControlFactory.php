<?php

declare(strict_types=1);

namespace App\UI\Shared\PragueDepartureTable\Control;

interface PragueDepartureTableListControlFactory
{
	public function create(): PragueDepartureTableListControl;
}
