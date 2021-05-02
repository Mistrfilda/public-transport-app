<?php

declare(strict_types=1);

namespace App\Transport\Prague\StopLine\Trip\Import;

use App\Doctrine\NoEntityFoundException;
use App\Transport\Prague\Stop\StopRepository;
use App\Transport\Prague\StopLine\Trip\TripFactory;
use App\Transport\Prague\StopLine\Trip\TripRepository;
use Doctrine\ORM\EntityManagerInterface;
use Mistrfilda\Datetime\DatetimeFactory;
use Mistrfilda\Pid\Api\GolemioService;
use Psr\Log\LoggerInterface;
use Throwable;

class TripImportFacade
{
	private GolemioService $pidService;

	private EntityManagerInterface $entityManager;

	private LoggerInterface $logger;

	private TripRepository $tripRepository;

	private TripFactory $tripFactory;

	private DatetimeFactory $datetimeFactory;

	private StopRepository $stopRepository;

	public function __construct(
		GolemioService $pidService,
		EntityManagerInterface $entityManager,
		LoggerInterface $logger,
		TripRepository $tripRepository,
		TripFactory $tripFactory,
		DatetimeFactory $datetimeFactory,
		StopRepository $stopRepository
	) {
		$this->pidService = $pidService;
		$this->entityManager = $entityManager;
		$this->logger = $logger;
		$this->tripRepository = $tripRepository;
		$this->tripFactory = $tripFactory;
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
				'Downloading new trip data for stop',
				[
					'date' => $date->format(DatetimeFactory::DEFAULT_DATETIME_FORMAT),
					'stop' => $stop->getId(),
					'stopId' => $stop->getStopId(),
				]
			);

			$count++;
			$trips = $this->pidService->sendGetStopTripsRequest($stop->getStopId(), 5_000, 0, $date);

			if ($trips->getCount() === 0) {
				continue;
			}

			$this->entityManager->beginTransaction();
			try {
				$index = 0;
				foreach ($trips->getTrips() as $trip) {
					try {
						$existingTrip = $this->tripRepository->findByStopDateTripId(
							$stop->getId(),
							$date,
							$trip->getTripId()
						);

						$existingTrip->updateTrip(
							$trip->getTripHeadsign(),
							$trip->isWheelchairAccessible()
						);
					} catch (NoEntityFoundException $e) {
						$newTrip = $this->tripFactory->createFromPidLibrary($trip, $stop, $date);
						$this->entityManager->persist($newTrip);
					}

					$index++;
					if ($index % 50 === 0) {
						$this->entityManager->flush();
					}
				}
			} catch (Throwable $e) {
				$this->entityManager->rollback();
				$this->logger->critical(
					'Exception occurred while trips for stop, rollbacking',
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
				'Downloading new trip data for stop successfully finished',
				[
					'date' => $date->format(DatetimeFactory::DEFAULT_DATETIME_FORMAT),
					'stop' => $stop->getId(),
					'stopId' => $stop->getStopId(),
				]
			);
		}
	}
}
