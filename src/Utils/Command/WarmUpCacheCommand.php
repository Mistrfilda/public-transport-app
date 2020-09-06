<?php

declare(strict_types=1);

namespace App\Utils\Command;

use App\Transport\Prague\Statistic\TripList\TripListCacheService;
use App\Transport\Prague\Stop\StopCacheService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WarmUpCacheCommand extends Command
{
	private TripListCacheService $tripListCacheService;

	private StopCacheService $stopCacheService;

	public function __construct(
		TripListCacheService $tripListCacheService,
		StopCacheService $stopCacheService
	) {
		parent::__construct();
		$this->tripListCacheService = $tripListCacheService;
		$this->stopCacheService = $stopCacheService;
	}

	protected function configure(): void
	{
		parent::configure();
		$this->setName('app:cache:load');
		$this->setDescription('Warm ups application cache');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$output->writeln('<info>Warming up trip list cache</info>');
		$this->tripListCacheService->loadCache(true);
		$output->writeln('<info>Warming up trip list cache finished</info>');
		$output->writeln('<info>Warming up stop cache service</info>');
		$this->stopCacheService->loadCache(true);
		$output->writeln('<info>Warming up stop cache service finished</info>');
		return 0;
	}
}
