<?php

declare(strict_types=1);

namespace App\UI\Admin\PragueDepartureTable;

use App\Transport\Prague\DepartureTable\DepartureTable;
use App\Transport\Prague\DepartureTable\DepartureTableFacade;
use App\UI\Admin\AdminPresenter;
use App\UI\Admin\Base\AdminDatagrid;
use App\UI\Admin\Base\AdminForm;
use App\UI\Admin\PragueDepartureTable\Control\DepartureTableControl;
use App\UI\Admin\PragueDepartureTable\Control\DepartureTableControlFactory;
use App\UI\Admin\PragueDepartureTable\Datagrid\DepartureTableDatagridFactory;
use App\UI\Admin\PragueDepartureTable\Exception\InvalidArgumentException;
use App\UI\Admin\PragueDepartureTable\Form\DepartureTableFormFactory;
use App\Utils\FlashMessageType;
use Ramsey\Uuid\Uuid;

class PragueDepartureTablePresenter extends AdminPresenter
{
	/** @var DepartureTableDatagridFactory */
	private $departureTableDatagridFactory;

	/** @var DepartureTableFormFactory */
	private $departureTableFormFactory;

	/** @var DepartureTableFacade */
	private $departureTableFacade;

	/** @var DepartureTableControlFactory */
	private $departureTableControlFactory;

	public function __construct(
		DepartureTableDatagridFactory $departureTableDatagridFactory,
		DepartureTableFormFactory $departureTableFormFactory,
		DepartureTableFacade $departureTableFacade,
		DepartureTableControlFactory $departureTableControlFactory
	) {
		parent::__construct();
		$this->departureTableDatagridFactory = $departureTableDatagridFactory;
		$this->departureTableFormFactory = $departureTableFormFactory;
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

	protected function createComponentDepartureTableForm(): AdminForm
	{
		$id = $this->getParameter('id');

		if ($id !== null) {
			$id = Uuid::fromString($id);
		}

		$onSuccess = function (DepartureTable $departureTable): void {
			$this->flashMessage(
				sprintf('Departure table %s successfuly saved', $departureTable->getId()->toString()),
				FlashMessageType::INFO
			);
			$this->redirect('default');
		};

		return $this->departureTableFormFactory->create($onSuccess, $id);
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
