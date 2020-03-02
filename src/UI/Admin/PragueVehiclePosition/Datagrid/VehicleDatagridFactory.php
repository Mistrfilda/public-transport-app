<?php

declare(strict_types=1);

namespace App\UI\Admin\PragueVehiclePosition\Datagrid;

use App\Transport\Prague\Vehicle\VehiclePositionRepository;
use App\Transport\Prague\Vehicle\VehicleRepository;
use App\UI\Admin\Base\AdminDatagrid;
use App\UI\Admin\Base\AdminDatagridFactory;
use Ramsey\Uuid\UuidInterface;

class VehicleDatagridFactory
{
    /** @var AdminDatagridFactory */
    private $adminDatagridFactory;

    /** @var VehicleRepository */
    private $vehicleRepository;

    /** @var VehiclePositionRepository */
    private $vehiclePositionRepository;

    public function __construct(
        AdminDatagridFactory $adminDatagridFactory,
        VehicleRepository $vehicleRepository,
        VehiclePositionRepository $vehiclePositionRepository
    ) {
        $this->adminDatagridFactory = $adminDatagridFactory;
        $this->vehicleRepository = $vehicleRepository;
        $this->vehiclePositionRepository = $vehiclePositionRepository;
    }

    public function create(UuidInterface $vehiclePositionId): AdminDatagrid
    {
        $grid = $this->adminDatagridFactory->create();

        $vehiclePosition = $this->vehiclePositionRepository->findById($vehiclePositionId);

        $qb = $this->vehicleRepository->createQueryBuilder();
        $qb->andWhere($qb->expr()->eq('vehicle.vehiclePosition', ':vehiclePosition'));
        $qb->setParameter('vehiclePosition', $vehiclePosition);

        $grid->setDataSource($qb);

        $grid->addColumnText('routeId', 'Route ID')->setFilterText();
        $grid->addColumnText('tripId', 'Trip ID')->setFilterText();
        $grid->addColumnText('finalStation', 'Final station')->setFilterText();
        $grid->addColumnText('delayInSeconds', 'Delay in seconds')->setSortable()->setFilterText();
        $grid->addColumnText('vehicleType', 'Vehicle type')->setFilterText();

        $grid->addColumnText('lastStopId', 'Last stop ID')->setFilterText();
        $grid->addColumnText('nextStopId', 'Next stop ID')->setFilterText();

        $grid->addColumnText('registrationNumber', 'Registration number')->setFilterText();
        $grid->addColumnText('company', 'Company')->setSortable()->setFilterText();

        return $grid;
    }
}
