<?php

declare(strict_types=1);

namespace App\Transport\Prague\TransportRestriction\Import;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TransportRestrictionImportCommand extends Command
{
	private TransportRestrictionImportFacade $transportRestrictionImportFacade;

	public function __construct(TransportRestrictionImportFacade $transportRestrictionImportFacade)
	{
		parent::__construct();
		$this->transportRestrictionImportFacade = $transportRestrictionImportFacade;
	}

	public function configure(): void
	{
		parent::configure();
		$this->setName('prague:import:transportRestriction');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$this->transportRestrictionImportFacade->import();
		return 0;
	}
}
