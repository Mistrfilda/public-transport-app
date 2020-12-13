<?php

declare(strict_types=1);

namespace App\Transport\Prague\DepartureTable;

use App\Request\IRequestFacade;
use App\Request\RequestConditions;
use App\Transport\Prague\Stop\StopRepository;
use Doctrine\ORM\EntityManagerInterface;
use Mistrfilda\Datetime\DatetimeFactory;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;

class DepartureTableFacade
{
	private EntityManagerInterface $entityManager;

	private LoggerInterface $logger;

	private DepartureTableRepository $departureTableRepository;

	private StopRepository $stopRepository;

	private DatetimeFactory $datetimeFactory;

	/** @var IRequestFacade[] */
	private array $requestFacades;

	/**
	 * @param IRequestFacade[] $requestFacades
	 */
	public function __construct(
		EntityManagerInterface $entityManager,
		LoggerInterface $logger,
		DepartureTableRepository $departureTableRepository,
		StopRepository $stopRepository,
		DatetimeFactory $datetimeFactory,
		array $requestFacades
	) {
		$this->entityManager = $entityManager;
		$this->logger = $logger;
		$this->departureTableRepository = $departureTableRepository;
		$this->stopRepository = $stopRepository;
		$this->datetimeFactory = $datetimeFactory;
		$this->requestFacades = $requestFacades;
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
		$departureTable = new DepartureTable(
			$stop,
			$numberOfFutureDays,
			$this->datetimeFactory->createNow()
		);

		$this->entityManager->persist($departureTable);
		$this->entityManager->flush();
		$this->entityManager->refresh($departureTable);

		foreach ($this->requestFacades as $requestFacade) {
			$requestFacade->generateRequests(
				new RequestConditions(
					[
						'generateDepartureTables' => true,
						'generateVehiclePositions' => false,
					],
					['departureTableId' => $departureTable->getId()->toString()]
				)
			);
		}

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

		$departureTable = $this->departureTableRepository->findById(Uuid::fromString($departureTableId));
		$departureTable->update($numberOfFutureDays, $this->datetimeFactory->createNow());

		$this->entityManager->flush();
		$this->entityManager->refresh($departureTable);

		return $departureTable;
	}

	public function deleteDepartureTable(string $departureTableId): void
	{
		$this->logger->info(
			'Deleting departure table',
			[
				'id' => $departureTableId,
			]
		);

		$departureTable = $this->departureTableRepository->findById(Uuid::fromString($departureTableId));

		$this->entityManager->remove($departureTable);
		$this->entityManager->flush();
	}
}
