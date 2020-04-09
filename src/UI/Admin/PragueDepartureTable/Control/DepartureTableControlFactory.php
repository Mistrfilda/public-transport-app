<?php

declare(strict_types=1);

namespace App\UI\Admin\PragueDepartureTable\Control;

interface DepartureTableControlFactory
{
	public function create(string $id): DepartureTableControl;
}
