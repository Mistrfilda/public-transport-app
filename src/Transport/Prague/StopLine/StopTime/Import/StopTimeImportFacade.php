<?php

declare(strict_types=1);

namespace App\Transport\Prague\StopLine\StopTime\Import;

use App\Doctrine\NoEntityFoundException;
use App\Transport\Prague\Stop\StopRepository;
use App\Transport\Prague\StopLine\StopTime\StopTimeFactory;
use App\Transport\Prague\StopLine\StopTime\StopTimeRepository;
use App\Utils\DatetimeFactory;
use Doctrine\ORM\EntityManagerInterface;
use Ofce\Pid\Api\PidService;
use Psr\Log\LoggerInterface;
use Throwable;

class StopTimeImportFacade
{
    /** @var PidService */
    private $pidService;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var LoggerInterface */
    private $logger;

    /** @var StopTimeRepository */
    private $stopTimeRepository;

    /** @var StopTimeFactory */
    private $stopTimeFactory;

    /** @var DatetimeFactory */
    private $datetimeFactory;

    /** @var StopRepository */
    private $stopRepository;

    public function __construct(
        PidService $pidService,
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
                $date = $today->modify('+ ' . $count . ' days');
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
            $stopTimeResponse = $this->pidService->sendGetStopTimesRequest($stop->getStopId(), 5000, 0, $date);

            if ($stopTimeResponse->getCount() === 0) {
                continue;
            }

            $this->entityManager->beginTransaction();
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
}