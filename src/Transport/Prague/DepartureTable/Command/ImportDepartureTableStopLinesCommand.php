<?php

declare(strict_types=1);

namespace App\Transport\Prague\DepartureTable\Command;

use App\Transport\Prague\DepartureTable\DepartureTableStopLineFacade;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportDepartureTableStopLinesCommand extends Command
{
	private DepartureTableStopLineFacade $departureTableStopLineFacade;

	public function __construct(DepartureTableStopLineFacade $departureTableStopLineFacade)
	{
		parent::__construct();
		$this->departureTableStopLineFacade = $departureTableStopLineFacade;
	}

	protected function configure(): void
	{
		parent::configure();
		$this->setName('prague:import:departureTable');

		$this->addArgument(
			'departureTableId',
			InputArgument::REQUIRED,
			'Departure table uuid'
		);
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$departureTableId = $input->getArgument('departureTableId');

		if (is_string($departureTableId) === false) {
			throw new InvalidArgumentException();
		}

		$output->writeln(
			sprintf('<info>Downloading stop line data for departure table %s</info>', $departureTableId));
		$this->departureTableStopLineFacade->downloadStoplinesForDepartureTable(
			Uuid::fromString($departureTableId)
		);

		$output->writeln(
			sprintf('<info>Downloading stop line data for departure table %s finished</info>', $departureTableId));
		return 0;
	}
}
