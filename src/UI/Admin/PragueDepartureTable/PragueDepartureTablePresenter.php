<?php

declare(strict_types=1);

namespace App\UI\Admin\PragueDepartureTable;

use App\UI\Admin\AdminPresenter;
use App\UI\Admin\Base\AdminDatagrid;
use App\UI\Admin\PragueDepartureTable\Datagrid\DepartureTableDatagridFactory;

class PragueDepartureTablePresenter extends AdminPresenter
{
    /** @var DepartureTableDatagridFactory */
    private $departureTableDatagridFactory;

    public function __construct(DepartureTableDatagridFactory $departureTableDatagridFactory)
    {
        parent::__construct();
        $this->departureTableDatagridFactory = $departureTableDatagridFactory;
    }

    public function createComponentDepartureTableGrid(): AdminDatagrid
    {
        return $this->departureTableDatagridFactory->create();
    }
}
