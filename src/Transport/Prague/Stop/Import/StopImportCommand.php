<?php

declare(strict_types=1);

namespace App\Transport\Prague\Stop\Import;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StopImportCommand extends Command
{
	/** @var StopImportFacade */
	private $stopImportFacade;

	public function __construct(StopImportFacade $stopImportFacade)
	{
		parent::__construct();
		$this->stopImportFacade = $stopImportFacade;
	}

	public function configure(): void
	{
		parent::configure();
		$this->setName('prague:import:stop');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$this->stopImportFacade->import();
		return 0;
	}
}
