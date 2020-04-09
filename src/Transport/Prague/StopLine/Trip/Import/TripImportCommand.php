<?php

declare(strict_types=1);

namespace App\Transport\Prague\StopLine\Trip\Import;

use Nette\InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TripImportCommand extends Command
{
	/** @var TripImportFacade */
	private $tripImportFacade;

	public function __construct(TripImportFacade $tripImportFacade)
	{
		parent::__construct(null);
		$this->tripImportFacade = $tripImportFacade;
	}

	protected function configure(): void
	{
		parent::configure();
		$this->setName('prague:import:trip');

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

		$this->tripImportFacade->import((int) $stopId, (int) $numberOfDays);
		return 0;
	}
}
