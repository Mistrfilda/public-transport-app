<?php

declare(strict_types=1);

namespace App\Transport\Prague\Request\RabbitMQ\Command;

use App\Request\RequestConditions;
use App\Transport\Prague\Request\RequestFacade;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateHalfHourRequestsCommand extends Command
{
	private RequestFacade $requestFacade;

	public function __construct(RequestFacade $requestFacade)
	{
		parent::__construct();
		$this->requestFacade = $requestFacade;
	}

	public function configure(): void
	{
		parent::configure();
		$this->setName('prague:requests:halfHour');
	}

	public function execute(InputInterface $input, OutputInterface $output): int
	{
		$output->writeln('<info>Generating 30 minute requests</info>');

		$conditions = new RequestConditions();

		$this->requestFacade->generateTransportRestrictionRequest($conditions);
		$this->requestFacade->generateParkingLotRequest($conditions);

		$output->writeln('<info>Requests successfully generated</info>');
		return 0;
	}
}
