<?php

declare(strict_types=1);

namespace App\Transport\Prague\Vehicle\Import;

use App\Transport\Prague\Vehicle\VehicleFactory;
use App\Transport\Prague\Vehicle\VehiclePosition;
use App\Utils\DatetimeFactory;
use Doctrine\ORM\EntityManagerInterface;
use Mistrfilda\Pid\Api\PidService;
use Psr\Log\LoggerInterface;
use Throwable;

class VehicleImportFacade
{
	/** @var EntityManagerInterface */
	private $entityManager;

	/** @var LoggerInterface */
	private $logger;

	/** @var PidService */
	private $pidService;

	/** @var VehicleFactory */
	private $vehicleFactory;

	/** @var DatetimeFactory */
	private $datetimeFactory;

	public function __construct(
		EntityManagerInterface $entityManager,
		LoggerInterface $logger,
		PidService $pidService,
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

			foreach ($vehiclePositionResponse->getVehiclePositions() as $currentVehiclePosition) {
				$vehicle = $this->vehicleFactory->createFromPidLibrary($currentVehiclePosition, $vehiclePosition);
				$this->entityManager->persist($vehicle);
			}

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
