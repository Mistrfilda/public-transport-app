<?php

declare(strict_types=1);

namespace App\UI\Admin\Prague\PragueVehiclePosition;

use App\UI\Admin\AdminPresenter;
use App\UI\Admin\Base\AdminDatagrid;
use App\UI\Admin\Prague\PragueVehiclePosition\Datagrid\VehicleDatagridFactory;
use App\UI\Admin\Prague\PragueVehiclePosition\Datagrid\VehiclePositionDatagridFactory;
use Nette\Application\BadRequestException;
use Ramsey\Uuid\Uuid;

class PragueVehiclePositionPresenter extends AdminPresenter
{
	private VehiclePositionDatagridFactory $vehiclePositionDatagridFactory;

	private VehicleDatagridFactory $vehicleDatagridFactory;

	public function __construct(
		VehiclePositionDatagridFactory $vehiclePositionDatagridFactory,
		VehicleDatagridFactory $vehicleDatagridFactory
	) {
		parent::__construct();
		$this->vehiclePositionDatagridFactory = $vehiclePositionDatagridFactory;
		$this->vehicleDatagridFactory = $vehicleDatagridFactory;
	}

	public function renderVehicle(string $id): void
	{
	}

	protected function createComponentVehiclePositionDatagrid(): AdminDatagrid
	{
		return $this->vehiclePositionDatagridFactory->create();
	}

	protected function createComponentVehicleDatagrid(): AdminDatagrid
	{
		$id = $this->getParameter('id');

		if ($id === null) {
			throw new BadRequestException('Invalid parameter ID');
		}

		$id = Uuid::fromString($id);

		return $this->vehicleDatagridFactory->create($id);
	}
}
