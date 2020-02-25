<?php

declare(strict_types=1);

namespace App\UI\Admin\PragueDepartureTable;

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

    public function __construct(
        DepartureTableDatagridFactory $departureTableDatagridFactory,
        DepartureTableFormFactory $departureTableFormFactory
    ) {
        parent::__construct();
        $this->departureTableDatagridFactory = $departureTableDatagridFactory;
        $this->departureTableFormFactory = $departureTableFormFactory;
    }

    public function renderEdit(?string $id): void
    {
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
