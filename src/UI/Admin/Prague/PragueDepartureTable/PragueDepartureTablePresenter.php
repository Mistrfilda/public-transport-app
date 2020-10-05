<?php

declare(strict_types=1);

namespace App\UI\Admin\Prague\PragueDepartureTable;

use App\Transport\Prague\DepartureTable\DepartureTableFacade;
use App\UI\Admin\AdminPresenter;
use App\UI\Admin\Base\AdminDatagrid;
use App\UI\Admin\Prague\PragueDepartureTable\Control\DepartureTableControl;
use App\UI\Admin\Prague\PragueDepartureTable\Control\DepartureTableControlFactory;
use App\UI\Admin\Prague\PragueDepartureTable\Datagrid\DepartureTableDatagridFactory;
use App\UI\Admin\Prague\PragueDepartureTable\Exception\InvalidArgumentException;

class PragueDepartureTablePresenter extends AdminPresenter
{
	private DepartureTableDatagridFactory $departureTableDatagridFactory;

	private DepartureTableFacade $departureTableFacade;

	private DepartureTableControlFactory $departureTableControlFactory;

	public function __construct(
		DepartureTableDatagridFactory $departureTableDatagridFactory,
		DepartureTableFacade $departureTableFacade,
		DepartureTableControlFactory $departureTableControlFactory
	) {
		parent::__construct();
		$this->departureTableDatagridFactory = $departureTableDatagridFactory;
		$this->departureTableFacade = $departureTableFacade;
		$this->departureTableControlFactory = $departureTableControlFactory;
	}

	public function renderEdit(?string $id): void
	{
	}

	public function renderTable(string $id): void
	{
	}

	public function handleDeleteDepartureTable(string $id): void
	{
		$this->departureTableFacade->deleteDepartureTable($id);
		$this->flashMessage('Departure table successfully deleted');
	}

	protected function createComponentDepartureTableGrid(): AdminDatagrid
	{
		return $this->departureTableDatagridFactory->create();
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
