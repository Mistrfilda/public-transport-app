<?php

declare(strict_types=1);

namespace App\Transport\Prague\Statistic\Command;

use App\Transport\Prague\Statistic\TripStatisticDataRepository;
use Mistrfilda\Datetime\Holiday\CzechHolidayService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

class UpdateTripStatisticDataDatetimeCommand extends Command
{
	private TripStatisticDataRepository $tripStatisticDataRepository;

	private CzechHolidayService $czechHolidayService;

	private EntityManagerInterface $entityManager;

	private LoggerInterface $logger;

	public function __construct(
		TripStatisticDataRepository $tripStatisticDataRepository,
		CzechHolidayService $czechHolidayService,
		EntityManagerInterface $entityManager,
		LoggerInterface $logger
	) {
		parent::__construct();
		$this->tripStatisticDataRepository = $tripStatisticDataRepository;
		$this->czechHolidayService = $czechHolidayService;
		$this->entityManager = $entityManager;
		$this->logger = $logger;
	}

	protected function configure(): void
	{
		parent::configure();
		$this->setName('prague:statistic:update:datetime');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$step = 0;

		while (true) {
			$allTripsStatisticData = $this->tripStatisticDataRepository->findAll(
				10000,
				$step
			);

			if (count($allTripsStatisticData) === 0) {
				break;
			}

			$progressBar = new ProgressBar($output, count($allTripsStatisticData));
			$this->logger->debug(
				'Updating datetime in trip statistic data',
				[
					'step' => $step,
				]
			);
			$index = 0;

			$this->entityManager->beginTransaction();
			try {
				foreach ($allTripsStatisticData as $tripData) {
					$tripData->updateDate(
						$this->czechHolidayService->isDateTimeHoliday($tripData->getDate())
					);

					$index++;
					if ($index === 100) {
						$this->entityManager->flush();
						$progressBar->advance(100);
					}
				}

				$progressBar->finish();
				$this->entityManager->flush();
				$this->entityManager->commit();
				$this->entityManager->clear();

				$step += 10000;
			} catch (Throwable $e) {
				$this->entityManager->rollback();
				throw $e;
			}
		}

		return 0;
	}
}
