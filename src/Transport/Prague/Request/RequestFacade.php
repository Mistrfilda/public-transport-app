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
use App\Transport\Prague\Request\RabbitMQ\ParkingLot\ParkingLotProducer;
use App\Transport\Prague\Request\RabbitMQ\TransportRestriction\TransportRestrictionProducer;
use App\Transport\Prague\Request\RabbitMQ\VehiclePosition\VehiclePositionProducer;
use App\Utils\Datetime\DatetimeFactory;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class RequestFacade implements IRequestFacade
{
	private LoggerInterface $logger;

	private DatetimeFactory $datetimeFactory;

	private DepartureTableRepository $departureTableRepository;

	private EntityManagerInterface $entityManager;

	private DepartureTableProducer $departureTableProducer;

	private VehiclePositionProducer $vehiclePositionProducer;

	private TransportRestrictionProducer $transportRestrictionProducer;

	private ParkingLotProducer $parkingLotProducer;

	private RequestRepository $requestRepository;

	public function __construct(
		LoggerInterface $logger,
		DatetimeFactory $datetimeFactory,
		DepartureTableRepository $departureTableRepository,
		RequestRepository $requestRepository,
		EntityManagerInterface $entityManager,
		DepartureTableProducer $departureTableProducer,
		VehiclePositionProducer $vehiclePositionProducer,
		TransportRestrictionProducer $transportRestrictionProducer,
		ParkingLotProducer $parkingLotProducer
	) {
		$this->logger = $logger;
		$this->datetimeFactory = $datetimeFactory;
		$this->departureTableRepository = $departureTableRepository;
		$this->entityManager = $entityManager;
		$this->departureTableProducer = $departureTableProducer;
		$this->vehiclePositionProducer = $vehiclePositionProducer;
		$this->requestRepository = $requestRepository;
		$this->transportRestrictionProducer = $transportRestrictionProducer;
		$this->parkingLotProducer = $parkingLotProducer;
	}

	public function generateRequests(RequestConditions $conditions): void
	{
		$this->logger->debug('Generating prague requests');
		$this->generateDepartureTableRequests($conditions);
		$this->generateVehiclePositionsRequest($conditions);
		$this->generateParkingLotRequest($conditions);
		$this->generateTransportRestrictionRequest($conditions);
	}

	public function generateDepartureTableRequests(RequestConditions $conditions): void
	{
		if (
			$conditions->hasCondition(DepartureTableProducer::FILTER_KEY)
			&& $conditions->getCondition(DepartureTableProducer::FILTER_KEY) === false
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

	public function generateVehiclePositionsRequest(RequestConditions $conditions): void
	{
		if (
			$conditions->hasCondition(VehiclePositionProducer::FILTER_KEY)
			&& $conditions->getCondition(VehiclePositionProducer::FILTER_KEY) === false
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

	public function generateTransportRestrictionRequest(RequestConditions $conditions): void
	{
		if (
			$conditions->hasCondition(TransportRestrictionProducer::FILTER_KEY)
			&& $conditions->getCondition(TransportRestrictionProducer::FILTER_KEY) === false
		) {
			return;
		}

		$this->logger->info('Generating transport restriction request');

		if (
			$this->requestRepository->findLastRequestByType(
				RequestType::PRAGUE_TRANSPORT_RESTRICTION,
				$this->datetimeFactory->createNow()
			) !== null
		) {
			$this->logger->debug(
				'Skipping creating of generate Generating transport restriction request, request already pending'
			);
			return;
		}

		$request = new Request(
			RequestType::PRAGUE_TRANSPORT_RESTRICTION,
			$this->datetimeFactory->createNow()
		);

		$this->entityManager->persist($request);
		$this->entityManager->flush();
		$this->entityManager->refresh($request);

		$this->transportRestrictionProducer->publish($request);
	}

	public function generateParkingLotRequest(RequestConditions $conditions): void
	{
		if (
			$conditions->hasCondition(ParkingLotProducer::FILTER_KEY)
			&& $conditions->getCondition(ParkingLotProducer::FILTER_KEY) === false
		) {
			return;
		}

		$this->logger->info('Generating parking lot request');

		if (
			$this->requestRepository->findLastRequestByType(
				RequestType::PRAGUE_PARKING_LOT,
				$this->datetimeFactory->createNow()
			) !== null
		) {
			$this->logger->debug(
				'Skipping creating of generate Generating tparking lot request, request already pending'
			);
			return;
		}

		$request = new Request(
			RequestType::PRAGUE_PARKING_LOT,
			$this->datetimeFactory->createNow()
		);

		$this->entityManager->persist($request);
		$this->entityManager->flush();
		$this->entityManager->refresh($request);

		$this->parkingLotProducer->publish($request);
	}
}
