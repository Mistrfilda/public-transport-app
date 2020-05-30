<?php

declare(strict_types=1);

namespace App\Transport\Prague\Statistic\TripList;

use App\Doctrine\NoEntityFoundException;
use App\Transport\Prague\Statistic\TripStatisticDataRepository;
use App\Utils\DatetimeFactory;
use Doctrine\ORM\EntityManagerInterface;
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
			$trips = $this->tripStatisticDataRepository->findTripList();
			$count = count($trips);
			$this->logger->info('Generating new trip list', ['count' => $count]);

			$progressBar = null;
			if ($outputInterface !== null) {
				$progressBar = new ProgressBar($outputInterface, $count);
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
						$this->datetimeFactory->createNow()
					);
				} catch (NoEntityFoundException $e) {
					$tripList = new TripList(
						$trip['tripId'],
						$trip['routeId'],
						$this->datetimeFactory->createDatetimeFromMysqlFormat($trip['newestKnownPosition']),
						$trip['finalStation'],
						$this->datetimeFactory->createNow()
					);

					$this->entityManager->persist($tripList);
				}

				if ($index > 100) {
					$this->entityManager->flush();
					$index = 0;

					if ($progressBar !== null) {
						$progressBar->advance(100);
					}
				}

				$index++;
			}

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
