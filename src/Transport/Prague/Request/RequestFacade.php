<?php

declare(strict_types=1);

namespace App\Transport\Prague\Request;

use App\Request\IRequestFacade;
use App\Request\Request;
use App\Request\RequestConditions;
use App\Request\RequestType;
use App\Transport\Prague\DepartureTable\DepartureTableRepository;
use App\Transport\Prague\Request\RabbitMQ\DepartureTable\DepartureTableProducer;
use App\Transport\Prague\Request\RabbitMQ\VehiclePosition\VehiclePositionProducer;
use App\Utils\DatetimeFactory;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class RequestFacade implements IRequestFacade
{
    /** @var LoggerInterface */
    private $logger;

    /** @var DatetimeFactory */
    private $datetimeFactory;

    /** @var DepartureTableRepository */
    private $departureTableRepository;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var DepartureTableProducer */
    private $departureTableProducer;

    /** @var VehiclePositionProducer */
    private $vehiclePositionProducer;

    public function __construct(
        LoggerInterface $logger,
        DatetimeFactory $datetimeFactory,
        DepartureTableRepository $departureTableRepository,
        EntityManagerInterface $entityManager,
        DepartureTableProducer $departureTableProducer,
        VehiclePositionProducer $vehiclePositionProducer
    ) {
        $this->logger = $logger;
        $this->datetimeFactory = $datetimeFactory;
        $this->departureTableRepository = $departureTableRepository;
        $this->entityManager = $entityManager;
        $this->departureTableProducer = $departureTableProducer;
        $this->vehiclePositionProducer = $vehiclePositionProducer;
    }

    public function generateRequests(RequestConditions $conditions): void
    {
        $this->logger->debug('Generating prague requests');
        $this->generateDepartureTableRequests($conditions);
        $this->generateVehiclePositionsRequest($conditions);
    }

    private function generateDepartureTableRequests(RequestConditions $conditions): void
    {
        foreach ($this->departureTableRepository->findAll() as $departureTable) {
            $this->logger->info('Generating departure table request', $departureTable->jsonSerialize());

            $request = new Request(
                RequestType::PRAGUE_DEPARTURE_TABLE,
                $this->datetimeFactory->createNow(),
                $departureTable
            );

            $this->entityManager->persist($request);
            $this->entityManager->flush();
            $this->entityManager->refresh($request);

            $this->departureTableProducer->publish($request);
        }
    }

    private function generateVehiclePositionsRequest(RequestConditions $conditions): void
    {
        $request = new Request(
            RequestType::PRAGUE_VEHICLE_POSITION,
            $this->datetimeFactory->createNow()
        );

        $this->entityManager->persist($request);
        $this->entityManager->flush();
        $this->entityManager->refresh($request);

        $this->vehiclePositionProducer->publish($request);
    }
}
