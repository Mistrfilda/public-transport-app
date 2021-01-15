<?php

declare(strict_types=1);

namespace App\Transport\Prague\Statistic\TripList;

use App\Doctrine\NoEntityFoundException;
use App\Transport\Prague\Statistic\TripStatisticDataRepository;
use Doctrine\ORM\EntityManagerInterface;
use Mistrfilda\Datetime\DatetimeFactory;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

class TripListFacade
{
	private DatetimeFactory $datetimeFactory;

	private TripListRepository $tripListRepository;

	private LoggerInterface $logger;

	private TripStatisticDataRepository $tripStatisticDataRepository;

	private EntityManagerInterface $entityManager;

	public function __construct(
		DatetimeFactory $datetimeFactory,
		TripListRepository $tripListRepository,
		LoggerInterface $logger,
		TripStatisticDataRepository $tripStatisticDataRepository,
		EntityManagerInterface $entityManager
	) {
		$this->datetimeFactory = $datetimeFactory;
		$this->tripListRepository = $tripListRepository;
		$this->logger = $logger;
		$this->tripStatisticDataRepository = $tripStatisticDataRepository;
		$this->entityManager = $entityManager;
	}

	public function generateTripList(?OutputInterface $outputInterface = null): void
	{
		$this->entityManager->beginTransaction();

		try {
			$count = $this->tripStatisticDataRepository->countForTripList();
			$this->logger->info('Generating new trip list', ['count' => $count]);
			$progressBar = null;
			if ($outputInterface !== null) {
				$progressBar = new ProgressBar($outputInterface, $count);
			}

			$step = 5000;
			$maxStep = 30000;
			$currentStep = 0;
			while ($currentStep < $maxStep) {
				$trips = $this->tripStatisticDataRepository->findTripList(
					$currentStep * $step, $step
				);

				if (count($trips) === 0) {
					break;
				}

				$index = 0;
				foreach ($trips as $trip) {
					try {
						$tripList = $this->tripListRepository->findByStopDateTripId(
							$trip['tripId'],
							$trip['routeId']
						);

						$tripList->update(
							$this->datetimeFactory->createDatetimeFromMysqlFormat($trip['newestKnownPosition']),
							$trip['finalStation'],
							$this->datetimeFactory->createNow(),
							(int) $trip['rowCount']
						);
					} catch (NoEntityFoundException $e) {
						$tripList = new TripList(
							$trip['tripId'],
							$trip['routeId'],
							$this->datetimeFactory->createDatetimeFromMysqlFormat($trip['newestKnownPosition']),
							$trip['finalStation'],
							$this->datetimeFactory->createNow(),
							(int) $trip['rowCount']
						);

						$this->entityManager->persist($tripList);
					}

					if ($index > 100) {
						$this->entityManager->flush();
						$this->entityManager->clear();

						$index = 0;

						if ($progressBar !== null) {
							$progressBar->advance(100);
						}
					}

					$index++;
				}

				$currentStep++;
			}

			$this->entityManager->flush();
			$this->entityManager->commit();
			$this->logger->info('Generating new trip list finished');
		} catch (Throwable $e) {
			$this->logger->critical('Generating new trip list failed', [
				'exception' => $e,
			]);
			$this->entityManager->rollback();
		}
	}
}
