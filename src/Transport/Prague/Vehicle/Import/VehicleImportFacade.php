<?php

declare(strict_types=1);

namespace App\Transport\Prague\Vehicle\Import;

use App\Transport\Prague\Vehicle\VehicleFactory;
use App\Transport\Prague\Vehicle\VehiclePosition;
use Doctrine\ORM\EntityManagerInterface;
use Mistrfilda\Datetime\DatetimeFactory;
use Mistrfilda\Pid\Api\GolemioService;
use Psr\Log\LoggerInterface;
use Throwable;

class VehicleImportFacade
{
	private EntityManagerInterface $entityManager;

	private LoggerInterface $logger;

	private GolemioService $pidService;

	private VehicleFactory $vehicleFactory;

	private DatetimeFactory $datetimeFactory;

	public function __construct(
		EntityManagerInterface $entityManager,
		LoggerInterface $logger,
		GolemioService $pidService,
		VehicleFactory $vehicleFactory,
		DatetimeFactory $datetimeFactory
	) {
		$this->entityManager = $entityManager;
		$this->logger = $logger;
		$this->pidService = $pidService;
		$this->vehicleFactory = $vehicleFactory;
		$this->datetimeFactory = $datetimeFactory;
	}

	public function import(): void
	{
		$this->logger->info('Sending vehicle position request');
		$vehiclePositionResponse = $this->pidService->sendGetVehiclePositionRequest(5000);

		$this->entityManager->beginTransaction();
		try {
			$vehiclePosition = new VehiclePosition($this->datetimeFactory->createNow());
			$this->entityManager->persist($vehiclePosition);
			$this->entityManager->flush();

			$this->logger->info(
				'Saving vehicle position',
				[
					'count' => $vehiclePositionResponse->getCount(),
				]
			);

			$vehiclesCount = 0;
			foreach ($vehiclePositionResponse->getVehiclePositions() as $currentVehiclePosition) {
				$vehicle = $this->vehicleFactory->createFromPidLibrary($currentVehiclePosition, $vehiclePosition);
				$this->entityManager->persist($vehicle);
				$vehiclesCount++;

				if ($vehiclesCount % 100 === 0) {
					$this->entityManager->flush();
				}
			}

			$vehiclePosition->updateVehiclesCount($vehiclesCount);
			$this->entityManager->flush();
			$this->entityManager->commit();
			$this->entityManager->refresh($vehiclePosition);
		} catch (Throwable $e) {
			$this->entityManager->rollback();
			$this->logger->critical(
				'Exception occurred while downloading vehicle positions, rollbacking',
				[
					'exception' => $e,
				]
			);

			throw $e;
		}

		$this->logger->info(
			'Downloading new vehicle positions successfully finished',
			[
				'vehiclePositionId' => $vehiclePosition->getId()->toString(),
			]
		);
	}
}
