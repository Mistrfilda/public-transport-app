<?php

declare(strict_types=1);

namespace App\Transport\Prague\DepartureTable;

use App\Transport\Prague\Stop\StopRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;

class DepartureTableFacade
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var LoggerInterface */
    private $logger;

    /** @var DepartureTableRepository */
    private $departureTableRepository;

    /** @var StopRepository */
    private $stopRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        DepartureTableRepository $departureTableRepository,
        StopRepository $stopRepository
    ) {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->departureTableRepository = $departureTableRepository;
        $this->stopRepository = $stopRepository;
    }

    public function createDepartureTable(int $stopId, int $numberOfFutureDays): DepartureTable
    {
        $this->logger->info(
            'Creating new departure table',
            [
                'stopId' => $stopId,
                'numberOfFutureDays' => $numberOfFutureDays,
            ]
        );

        $stop = $this->stopRepository->findById($stopId);
        $departureTable = new DepartureTable($stop, $numberOfFutureDays);

        $this->entityManager->persist($departureTable);
        $this->entityManager->flush();
        $this->entityManager->refresh($departureTable);

        return $departureTable;
    }

    public function updateDepartureTable(string $departureTableId, int $numberOfFutureDays): DepartureTable
    {
        $this->logger->info(
            'Updating departure table',
            [
                'id' => $departureTableId,
                'numberOfFutureDays' => $numberOfFutureDays,
            ]
        );

        $departureTable = $this->departureTableRepository->findByStopId(Uuid::fromString($departureTableId));
        $departureTable->update($numberOfFutureDays);

        $this->entityManager->flush();
        $this->entityManager->refresh($departureTable);

        return $departureTable;
    }
}
