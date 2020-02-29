<?php

declare(strict_types=1);

namespace App\UI\Admin\PragueDepartureTable\Datagrid;

use App\Transport\Prague\DepartureTable\DepartureTableRepository;
use App\Transport\Prague\Stop\StopRepository;
use App\UI\Admin\Base\AdminDatagrid;
use App\UI\Admin\Base\AdminDatagridFactory;
use Ublaboo\DataGrid\Column\Action\Confirmation\StringConfirmation;

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

        $grid->addAction('edit', 'Edit', 'edit')
            ->setIcon('cog')
            ->setClass('btn btn-sm btn-primary');

        $grid->addAction('delete', 'Delete', 'deleteDepartureTable!')
            ->setIcon('trash')
            ->setClass('btn btn-sm btn-danger')
            ->setConfirmation(new StringConfirmation('Do you realy want to delete departure table %s', 'id'));

        $grid->addAction('detail', 'Show table', 'table')
            ->setIcon('eye')
            ->setClass('btn btn-sm btn-info');

        return $grid;
    }
}
