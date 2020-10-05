<?php

declare(strict_types=1);

namespace App\UI\Front\PragueDepartureTable;

use App\UI\Admin\Prague\PragueDepartureTable\Control\DepartureTableControl;
use App\UI\Admin\Prague\PragueDepartureTable\Control\DepartureTableControlFactory;
use App\UI\Admin\Prague\PragueDepartureTable\Exception\InvalidArgumentException;
use App\UI\Front\FrontPresenter;

class PragueDepartureTablePresenter extends FrontPresenter
{
	private DepartureTableControlFactory $departureTableControlFactory;

	public function __construct(
		DepartureTableControlFactory $departureTableControlFactory
	) {
		parent::__construct();
		$this->departureTableControlFactory = $departureTableControlFactory;
	}

	public function renderDetail(string $id): void
	{
	}

	protected function createComponentDepartureTableControl(): DepartureTableControl
	{
		$id = $this->getParameter('id');
		if ($id === null) {
			throw new InvalidArgumentException('Missing parameter ID');
		}

		return $this->departureTableControlFactory->create($id);
	}
}
