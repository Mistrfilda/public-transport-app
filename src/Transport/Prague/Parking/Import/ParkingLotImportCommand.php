<?php

declare(strict_types=1);

namespace App\Transport\Prague\Parking\Import;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ParkingLotImportCommand extends Command
{
	private ParkingLotImportFacade $parkingLotImportFacade;

	public function __construct(ParkingLotImportFacade $parkingLotImportFacade)
	{
		parent::__construct();
		$this->parkingLotImportFacade = $parkingLotImportFacade;
	}

	public function configure(): void
	{
		parent::configure();
		$this->setName('prague:import:parkingLots');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$this->parkingLotImportFacade->import();
		return 0;
	}
}
