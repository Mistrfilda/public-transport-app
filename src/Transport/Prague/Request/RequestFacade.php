<?php

declare(strict_types=1);

namespace App\Transport\Prague\Request;

use App\Request\IRequestFacade;
use App\Request\Request;
use App\Request\RequestConditions;
use App\Request\RequestRepository;
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

    /** @var RequestRepository */
    private $requestRepository;

    public function __construct(
        LoggerInterface $logger,
        DatetimeFactory $datetimeFactory,
        DepartureTableRepository $departureTableRepository,
        RequestRepository $requestRepository,
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
        $this->requestRepository = $requestRepository;
    }

    public function generateRequests(RequestConditions $conditions): void
    {
        $this->logger->debug('Generating prague requests');
        $this->generateDepartureTableRequests($conditions);
        $this->generateVehiclePositionsRequest($conditions);
    }

    private function generateDepartureTableRequests(RequestConditions $conditions): void
    {
        if (
            $conditions->hasCondition('generateDepartureTables')
            && $conditions->getCondition('generateDepartureTables') === false
        ) {
            return;
        }

        $departureTableId = null;
        if ($conditions->hasParameter('departureTableId')) {
            $departureTableId = $conditions->getParameter('departureTableId');
        }

        foreach ($this->departureTableRepository->findAll() as $departureTable) {
            if ($departureTableId !== null && $departureTable->getId()->toString() !== $departureTableId) {
                $this->logger->debug(
                    'Skiping generating request for departure table from conditions',
                    $departureTable->jsonSerialize()
                );
                continue;
            }

            $this->logger->info('Generating departure table request', $departureTable->jsonSerialize());

            if (
                $this->requestRepository->findLastRequestByTypeAndDepartureTable(
                    RequestType::PRAGUE_DEPARTURE_TABLE,
                    $departureTable,
                    $this->datetimeFactory->createNow()
                ) !== null
            ) {
                $this->logger->debug(
                    'Skipping creating of generate departure table request, request already pending',
                    [
                        'departureTable' => $departureTable->jsonSerialize(),
                    ]
                );
                continue;
            }

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
        if (
            $conditions->hasCondition('generateVehiclePositions')
            && $conditions->getCondition('generateVehiclePositions') === false
        ) {
            return;
        }

        $this->logger->info('Generating vehicle position request');

        if (
            $this->requestRepository->findLastRequestByType(
                RequestType::PRAGUE_VEHICLE_POSITION,
                $this->datetimeFactory->createNow()
            ) !== null
        ) {
            $this->logger->debug(
                'Skipping creating of generate vehicle position request, request already pending'
            );
            return;
        }

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
