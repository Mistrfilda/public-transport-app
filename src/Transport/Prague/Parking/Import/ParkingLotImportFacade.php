<?php

declare(strict_types=1);

namespace App\Transport\Prague\Parking\Import;

use App\Doctrine\NoEntityFoundException;
use App\Transport\Prague\Parking\ParkingLotFactory;
use App\Transport\Prague\Parking\ParkingLotOccupancyFactory;
use App\Transport\Prague\Parking\ParkingLotRepository;
use Doctrine\ORM\EntityManagerInterface;
use Mistrfilda\Pid\Api\GolemioService;
use Psr\Log\LoggerInterface;
use Throwable;

class ParkingLotImportFacade
{
	private GolemioService $golemioService;

	private EntityManagerInterface $entityManager;

	private ParkingLotFactory $parkingLotFactory;

	private ParkingLotOccupancyFactory $parkingLotOccupancyFactory;

	private ParkingLotRepository $parkingLotRepository;

	private LoggerInterface $logger;

	public function __construct(
		GolemioService $golemioService,
		EntityManagerInterface $entityManager,
		ParkingLotFactory $parkingLotFactory,
		ParkingLotOccupancyFactory $parkingLotOccupancyFactory,
		LoggerInterface $logger,
		ParkingLotRepository $parkingLotRepository
	) {
		$this->golemioService = $golemioService;
		$this->entityManager = $entityManager;
		$this->parkingLotFactory = $parkingLotFactory;
		$this->parkingLotOccupancyFactory = $parkingLotOccupancyFactory;
		$this->logger = $logger;
		$this->parkingLotRepository = $parkingLotRepository;
	}

	public function import(): void
	{
		$this->logger->info('Importing parking lot from pid library');

		$this->entityManager->beginTransaction();
		try {
			$parkings = $this->golemioService->sendGetParkingLotRequest();

			$this->logger->info('Successfully fetched parkings', [
				'count' => $parkings->getCount(),
			]);

			foreach ($parkings->getParkingLots() as $parking) {
				try {
					$parkingLot = $this->parkingLotRepository->getByParkingId($parking->getParkingId());
					$parkingLot->update(
						$parking->getLatitude(),
						$parking->getLongitude(),
						$parking->getFormattedAddress(),
						(string) $parking->getParkingTypeId(),
						$parking->getName(),
						$parking->getPaymentLink()
					);
				} catch (NoEntityFoundException $e) {
					$parkingLot = $this->parkingLotFactory->createFromPidLibrary($parking);
					$this->entityManager->persist($parkingLot);
				}

				$parkingLotOccupancy = $this->parkingLotOccupancyFactory->create(
					$parkingLot,
					$parking->getTotalNumberOfPlaces(),
					$parking->getFreePlaces(),
					$parking->getTakenPlaces()
				);

				$this->entityManager->persist($parkingLotOccupancy);
				$this->entityManager->flush();
			}

			$this->entityManager->commit();
		} catch (Throwable $e) {
			$this->entityManager->rollback();
			throw $e;
		}

		$this->logger->info('Parking lots successfully imported');
	}
}
