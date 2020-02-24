<?php

declare(strict_types=1);

namespace App\UI\Admin\PragueStop\Datagrid;

use App\Transport\Prague\Stop\StopRepository;
use App\UI\Admin\Base\AdminDatagrid;
use App\UI\Admin\Base\AdminDatagridFactory;

class StopDatagridFactory
{
    /** @var StopRepository */
    private $stopRepository;

    /** @var AdminDatagridFactory */
    private $adminDatagridFactory;

    public function __construct(StopRepository $stopRepository, AdminDatagridFactory $adminDatagridFactory)
    {
        $this->stopRepository = $stopRepository;
        $this->adminDatagridFactory = $adminDatagridFactory;
    }

    public function create(): AdminDatagrid
    {
        $grid = $this->adminDatagridFactory->create();

        $grid->setDataSource($this->stopRepository->createQueryBuilder());

        $grid->addColumnText('id', 'ID')->setSortable()->setFilterText();
        $grid->addColumnText('stopId', 'Stop ID')->setFilterText();
        $grid->addColumnText('name', 'Name')->setSortable()->setFilterText();

        return $grid;
    }
}
