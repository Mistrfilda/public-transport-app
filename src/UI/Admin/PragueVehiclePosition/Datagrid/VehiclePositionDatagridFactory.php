<?php

declare(strict_types=1);

namespace App\UI\Admin\PragueVehiclePosition\Datagrid;

use App\Transport\Prague\Vehicle\VehiclePositionRepository;
use App\UI\Admin\Base\AdminDatagrid;
use App\UI\Admin\Base\AdminDatagridFactory;

class VehiclePositionDatagridFactory
{
    /** @var AdminDatagridFactory */
    private $adminDatagridFactory;

    /** @var VehiclePositionRepository */
    private $vehiclePositionRepository;

    public function __construct(
        AdminDatagridFactory $adminDatagridFactory,
        VehiclePositionRepository $vehiclePositionRepository
    ) {
        $this->adminDatagridFactory = $adminDatagridFactory;
        $this->vehiclePositionRepository = $vehiclePositionRepository;
    }

    public function create(): AdminDatagrid
    {
        $grid = $this->adminDatagridFactory->create();
        $grid->setDataSource($this->vehiclePositionRepository->createQueryBuilder());

        $grid->addColumnText('id', 'ID');
        $grid->addColumnDateTime('createdAt', 'Created at')->setSortable()->setFilterDate();

        $grid->setDefaultSort(['createdAt' => 'desc']);

        $grid->addAction('vehicle', 'Vehicles', 'vehicle')
            ->setIcon('bus')
            ->setClass('btn btn-sm btn-primary');

        return $grid;
    }
}
