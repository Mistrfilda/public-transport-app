<?php

declare(strict_types=1);

namespace App\Transport\Prague\Statistic\Command;

use App\Transport\Prague\Statistic\TripList\TripListFacade;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateTripListCommand extends Command
{
	private TripListFacade $tripListFacade;

	public function __construct(TripListFacade $tripListFacade)
	{
		parent::__construct(null);
		$this->tripListFacade = $tripListFacade;
	}

	public function configure(): void
	{
		parent::configure();
		$this->setName('prague:statistic:generateTripList');
	}

	public function execute(InputInterface $input, OutputInterface $output): int
	{
		$this->tripListFacade->generateTripList($output);
		return 0;
	}
}
