<?php

declare(strict_types=1);

namespace App\Transport\Prague\StopLine\StopTime\Import;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StopTimeImportCommand extends Command
{
	/** @var StopTimeImportFacade */
	private $stopTimeImportFacade;

	public function __construct(StopTimeImportFacade $stopTimeImportFacade)
	{
		parent::__construct();
		$this->stopTimeImportFacade = $stopTimeImportFacade;
	}

	protected function configure(): void
	{
		parent::configure();
		$this->setName('prague:import:stopTimes');

		$this->addArgument(
			'stopId',
			InputArgument::REQUIRED,
			'Stop ID (int from database)'
		);

		$this->addArgument(
			'numberOfDays',
			InputArgument::REQUIRED,
			'Number of days to download'
		);
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$stopId = $input->getArgument('stopId');
		$numberOfDays = $input->getArgument('numberOfDays');

		if (is_string($stopId) === false || is_string($numberOfDays) === false) {
			throw new InvalidArgumentException();
		}

		$this->stopTimeImportFacade->import((int) $stopId, (int) $numberOfDays);
		return 0;
	}
}
