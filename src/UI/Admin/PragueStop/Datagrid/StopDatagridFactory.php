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

        $id = $grid->addColumnText('id', 'ID')->setSortable();
        $grid->setFilterSelect($id, [1 => 'aa', 2 => 'bb', 3 => 'gg'])->setPrompt('select');

        $grid->addColumnText('stopId', 'Stop ID')->setFilterDate();
        $grid->addColumnText('name', 'Name')->setSortable()->setFilterText();

        $grid->setAutoSubmit(false);
        $grid->setOuterFilterRendering();

        $grid->setRememberState(false);

        return $grid;
    }
}
