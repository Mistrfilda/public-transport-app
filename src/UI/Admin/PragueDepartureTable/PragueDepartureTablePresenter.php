<?php

declare(strict_types=1);

namespace App\UI\Admin\PragueDepartureTable;

use App\Transport\Prague\DepartureTable\DepartureTableFacade;
use App\UI\Admin\AdminPresenter;
use App\UI\Admin\Base\AdminDatagrid;
use App\UI\Admin\Base\AdminForm;
use App\UI\Admin\PragueDepartureTable\Datagrid\DepartureTableDatagridFactory;
use App\UI\Admin\PragueDepartureTable\Form\DepartureTableFormFactory;
use Ramsey\Uuid\Uuid;

class PragueDepartureTablePresenter extends AdminPresenter
{
    /** @var DepartureTableDatagridFactory */
    private $departureTableDatagridFactory;

    /** @var DepartureTableFormFactory */
    private $departureTableFormFactory;

    /** @var DepartureTableFacade */
    private $departureTableFacade;

    public function __construct(
        DepartureTableDatagridFactory $departureTableDatagridFactory,
        DepartureTableFormFactory $departureTableFormFactory,
        DepartureTableFacade $departureTableFacade
    ) {
        parent::__construct();
        $this->departureTableDatagridFactory = $departureTableDatagridFactory;
        $this->departureTableFormFactory = $departureTableFormFactory;
        $this->departureTableFacade = $departureTableFacade;
    }

    public function renderEdit(?string $id): void
    {
    }

    public function handleDeleteDepartureTable(string $id): void
    {
        $this->departureTableFacade->deleteDepartureTable($id);
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

        $onSuccess = function (): void {
            $this->flashMessage('Successfully saved');
            $this->redirect('default');
        };

        return $this->departureTableFormFactory->create($onSuccess, $id);
    }
}
