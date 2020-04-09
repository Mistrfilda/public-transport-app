<?php

declare(strict_types=1);

namespace App\Transport\Prague\StopLine\Trip\Import;

use App\Doctrine\NoEntityFoundException;
use App\Transport\Prague\Stop\StopRepository;
use App\Transport\Prague\StopLine\Trip\TripFactory;
use App\Transport\Prague\StopLine\Trip\TripRepository;
use App\Utils\DatetimeFactory;
use Doctrine\ORM\EntityManagerInterface;
use Mistrfilda\Pid\Api\PidService;
use Psr\Log\LoggerInterface;
use Throwable;

class TripImportFacade
{
	/** @var PidService */
	private $pidService;

	/** @var EntityManagerInterface */
	private $entityManager;

	/** @var LoggerInterface */
	private $logger;

	/** @var TripRepository */
	private $tripRepository;

	/** @var TripFactory */
	private $tripFactory;

	/** @var DatetimeFactory */
	private $datetimeFactory;

	/** @var StopRepository */
	private $stopRepository;

	public function __construct(
		PidService $pidService,
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
				$date = $today->modify('+ ' . $count . ' days');
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
			$trips = $this->pidService->sendGetStopTripsRequest($stop->getStopId(), 5000, 0, $date);

			if ($trips->getCount() === 0) {
				continue;
			}

			$this->entityManager->beginTransaction();
			try {
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
