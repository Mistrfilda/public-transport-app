<?php

declare(strict_types=1);

namespace App\Transport\Prague\Vehicle\Import;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class VehicleImportCommand extends Command
{
	/** @var VehicleImportFacade */
	private $vehicleImportFacade;

	public function __construct(VehicleImportFacade $vehicleImportFacade)
	{
		parent::__construct();
		$this->vehicleImportFacade = $vehicleImportFacade;
	}

	public function configure(): void
	{
		parent::configure();
		$this->setName('prague:import:vehicle');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$this->vehicleImportFacade->import();
		return 0;
	}
}
