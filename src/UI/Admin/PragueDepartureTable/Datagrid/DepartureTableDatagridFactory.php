<?php

declare(strict_types=1);

namespace App\UI\Admin\PragueDepartureTable\Datagrid;

use App\Transport\Prague\DepartureTable\DepartureTableRepository;
use App\Transport\Prague\Stop\StopRepository;
use App\UI\Admin\Base\AdminDatagrid;
use App\UI\Admin\Base\AdminDatagridFactory;

class DepartureTableDatagridFactory
{
    /** @var AdminDatagridFactory */
    private $adminDatagridFactory;

    /** @var DepartureTableRepository */
    private $departureTableRepository;

    /** @var StopRepository */
    private $stopRepository;

    public function __construct(
        AdminDatagridFactory $adminDatagridFactory,
        DepartureTableRepository $departureTableRepository,
        StopRepository $stopRepository
    ) {
        $this->adminDatagridFactory = $adminDatagridFactory;
        $this->departureTableRepository = $departureTableRepository;
        $this->stopRepository = $stopRepository;
    }

    public function create(): AdminDatagrid
    {
        $grid = $this->adminDatagridFactory->create();
        $qb = $this->departureTableRepository->createQueryBuilder();
        $qb->innerJoin('departureTable.stop', 'stop');
        $grid->setDataSource($qb);

        $grid->addColumnText('id', 'ID');
        $stopId = $grid->addColumnText('stopId', 'Stop ID', 'stop.stopId');
        $grid->setFilterSelect($stopId, $this->stopRepository->findStopIdPairs());

        $grid->addColumnText('stop', 'Stop', 'stop.name')->setFilterText();
        $grid->addColumnText('numberOfFutureDays', 'Number of future days');

        return $grid;
    }
}
