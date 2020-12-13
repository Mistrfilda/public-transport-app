<?php

declare(strict_types=1);

namespace App\Transport\Prague\StopLine\StopTime\Import;

use App\Doctrine\NoEntityFoundException;
use App\Transport\Prague\Stop\StopRepository;
use App\Transport\Prague\StopLine\StopTime\StopTimeFactory;
use App\Transport\Prague\StopLine\StopTime\StopTimeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Mistrfilda\Datetime\DatetimeFactory;
use Mistrfilda\Pid\Api\GolemioService;
use Psr\Log\LoggerInterface;
use Throwable;

class StopTimeImportFacade
{
	private GolemioService $pidService;

	private EntityManagerInterface $entityManager;

	private LoggerInterface $logger;

	private StopTimeRepository $stopTimeRepository;

	private StopTimeFactory $stopTimeFactory;

	private DatetimeFactory $datetimeFactory;

	private StopRepository $stopRepository;

	public function __construct(
		GolemioService $pidService,
		EntityManagerInterface $entityManager,
		LoggerInterface $logger,
		StopTimeRepository $stopTimeRepository,
		StopTimeFactory $stopTimeFactory,
		DatetimeFactory $datetimeFactory,
		StopRepository $stopRepository
	) {
		$this->pidService = $pidService;
		$this->entityManager = $entityManager;
		$this->logger = $logger;
		$this->stopTimeRepository = $stopTimeRepository;
		$this->stopTimeFactory = $stopTimeFactory;
		$this->datetimeFactory = $datetimeFactory;
		$this->stopRepository = $stopRepository;
	}

	public function import(int $stopId, int $numberOfDays = 1): void
	{
		$stop = $this->stopRepository->findById($stopId);
		$today = $this->datetimeFactory->createToday();

		$count = 0;
		while ($count < $numberOfDays) {
			$date = $today;
			if ($count >= 1) {
				$date = $today->addDaysToDatetime($count);
			}

			$this->logger->info(
				'Downloading new stop time data for stop',
				[
					'date' => $date->format(DatetimeFactory::DEFAULT_DATETIME_FORMAT),
					'stop' => $stop->getId(),
					'stopId' => $stop->getStopId(),
				]
			);

			$count++;
			$stopTimeResponse = $this->pidService->sendGetStopTimesRequest($stop->getStopId(), 5_000, 0, $date);

			if ($stopTimeResponse->getCount() === 0) {
				continue;
			}

			$this->entityManager->beginTransaction();

			$allExistingStopTimes = $this->stopTimeRepository->findIdsByDate(
				$stop->getId(),
				$date
			);

			try {
				foreach ($stopTimeResponse->getStopTimes() as $stopTime) {
					try {
						$existingStopTime = $this->stopTimeRepository->findByStopDateTripId(
							$stop->getId(),
							$date,
							$stopTime->getTripId()
						);

						$this->stopTimeFactory->update(
							$existingStopTime,
							$stopTime->getArivalTime(),
							$stopTime->getDepartureTime(),
							$date,
							$stopTime->getStopSequence()
						);
					} catch (NoEntityFoundException $e) {
						$newStopTime = $this->stopTimeFactory->createFromPidLibrary($stopTime, $stop, $date);
						$this->entityManager->persist($newStopTime);
					}

					//DELETE STOP TIMES WHICH WERE DELETED FROM TIME SCHEDULE
					if (array_key_exists($stopTime->getTripId(), $allExistingStopTimes)) {
						unset($allExistingStopTimes[$stopTime->getTripId()]);
					}
				}

				if (count($allExistingStopTimes) > 0) {
					$this->deleteOldStopTimes($allExistingStopTimes);
				}
			} catch (Throwable $e) {
				$this->entityManager->rollback();
				$this->logger->critical(
					'Exception occurred while downloading stop times for stop, rollbacking',
					[
						'exception' => $e,
						'date' => $date->format(DatetimeFactory::DEFAULT_DATETIME_FORMAT),
						'stop' => $stop->getId(),
						'stopId' => $stop->getStopId(),
					]
				);
				throw $e;
			}

			$this->entityManager->flush();
			$this->entityManager->commit();

			$this->logger->info(
				'Downloading new stop time data for stop successfully finished',
				[
					'date' => $date->format(DatetimeFactory::DEFAULT_DATETIME_FORMAT),
					'stop' => $stop->getId(),
					'stopId' => $stop->getStopId(),
				]
			);
		}
	}

	/**
	 * @param array<string, int> $stopTimesIds
	 */
	private function deleteOldStopTimes(array $stopTimesIds): void
	{
		$stopTimes = $this->stopTimeRepository->findByIds($stopTimesIds);
		foreach ($stopTimes as $stopTime) {
			$this->logger->debug(
				'Removing old stoptime because stoptime doesn\'t exists in new schedule',
				[
					'tripId' => $stopTime->getTripId(),
					'date' => $stopTime->getDate(),
					'departuretTime' => $stopTime->getDepartureTime(),
				]
			);

			$this->entityManager->remove($stopTime);
		}
	}
}
