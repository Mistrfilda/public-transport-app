<?php

declare(strict_types=1);

namespace App\Transport\Prague\Stop\Import;

use App\Doctrine\NoEntityFoundException;
use App\Transport\Prague\Stop\StopFactory;
use App\Transport\Prague\Stop\StopRepository;
use Doctrine\ORM\EntityManagerInterface;
use Mistrfilda\Pid\Api\PidService;
use Psr\Log\LoggerInterface;
use Throwable;

class StopImportFacade
{
	/** @var PidService */
	private $pidApiService;

	/** @var EntityManagerInterface */
	private $entityManager;

	/** @var StopRepository */
	private $stopRepository;

	/** @var StopFactory */
	private $stopFactory;

	/** @var LoggerInterface */
	private $logger;

	public function __construct(
		PidService $pidService,
		EntityManagerInterface $entityManager,
		StopRepository $stopRepository,
		StopFactory $stopFactory,
		LoggerInterface $logger
	) {
		$this->pidApiService = $pidService;
		$this->entityManager = $entityManager;
		$this->stopRepository = $stopRepository;
		$this->stopFactory = $stopFactory;
		$this->logger = $logger;
	}

	public function import(): void
	{
		$this->logger->info('Importing stops from pid api');

		//Proccessing 5000 result because thats maxximum of results which api can send back
		$step = 5000;
		$maxStep = 30000;
		$currentStep = 0;
		$noResult = false;

		while ($currentStep < $maxStep) {
			$this->entityManager->beginTransaction();
			try {
				$this->logger->info(
					'Sending request get stops',
					[
						'step' => $step,
						'currentStep' => $currentStep,
					]
				);

				$stopResponse = $this->pidApiService->sendGetStopsRequest(
					$step,
					$currentStep
				);

				if ($stopResponse->getCount() === 0) {
					$noResult = true;
					break;
				}

				foreach ($stopResponse->getStops() as $stop) {
					try {
						$existingStop = $this->stopRepository->findByStopId($stop->getStopId());
						$existingStop->updateStop(
							$stop->getName(),
							$stop->getLatitude(),
							$stop->getLongitude()
						);
					} catch (NoEntityFoundException $e) {
						$newStop = $this->stopFactory->createFromPidLibrary($stop);
						$this->entityManager->persist($newStop);
					}
				}

				$currentStep += $step;

				$this->entityManager->flush();
				$this->entityManager->commit();

				$this->logger->info(
					'Stops successfully imported',
					[
						'step' => $step,
						'currentStep' => $currentStep,
					]
				);
			} catch (Throwable $e) {
				$this->entityManager->rollback();
				$this->logger->critical('Exception occurred while importing stops, rollbacking', ['exception' => $e]);
				throw $e;
			}
		}

		$this->logger->info(
			'Import stops finished',
			[
				'step' => $step,
				'currentStep' => $currentStep,
				'noResult' => $noResult,
			]
		);
	}
}
