<?php

declare(strict_types=1);

namespace App\Transport\Prague\TransportRestriction\Import;

use App\Doctrine\NoEntityFoundException;
use App\Transport\Prague\TransportRestriction\TransportRestriction;
use App\Transport\Prague\TransportRestriction\TransportRestrictionFactory;
use App\Transport\Prague\TransportRestriction\TransportRestrictionRepository;
use App\Transport\TransportRestriction\TransportRestrictionPriority;
use App\Transport\TransportRestriction\TransportRestrictionType;
use Doctrine\ORM\EntityManagerInterface;
use Mistrfilda\Datetime\DatetimeFactory;
use Mistrfilda\Pid\Api\RssService;
use Psr\Log\LoggerInterface;
use Throwable;

class TransportRestrictionImportFacade
{
	private RssService $rssService;

	private EntityManagerInterface $entityManager;

	private LoggerInterface $logger;

	private TransportRestrictionFactory $transportRestrictionFactory;

	private TransportRestrictionRepository $transportRestrictionRepository;

	private DatetimeFactory $datetimeFactory;

	public function __construct(
		RssService $rssService,
		EntityManagerInterface $entityManager,
		LoggerInterface $logger,
		TransportRestrictionFactory $transportRestrictionFactory,
		TransportRestrictionRepository $transportRestrictionRepository,
		DatetimeFactory $datetimeFactory
	) {
		$this->rssService = $rssService;
		$this->entityManager = $entityManager;
		$this->logger = $logger;
		$this->transportRestrictionFactory = $transportRestrictionFactory;
		$this->transportRestrictionRepository = $transportRestrictionRepository;
		$this->datetimeFactory = $datetimeFactory;
	}

	public function import(): void
	{
		$this->importShortTerm();
		$this->importLongTerm();
	}

	public function importShortTerm(): void
	{
		$this->logger->info('Importing short term restrictions from pid library');

		$this->entityManager->beginTransaction();
		try {
			$shortTermRestrictions = $this->rssService->getShortTermRestrictions();
			$currentlyActiveRestrictions = $this->transportRestrictionRepository->findActiveByType(
				TransportRestrictionType::SHORT_TERM
			);

			$this->logger->info('Successfully fetched short term restrictions', [
				'count' => count($shortTermRestrictions),
				'currentlyActiveRestriction' => count($currentlyActiveRestrictions),
			]);

			foreach ($shortTermRestrictions as $shortTermTransportRestriction) {
				try {
					$transportRestriction = $this->transportRestrictionRepository->getTransportRestrictionId(
						$shortTermTransportRestriction->getGuid()
					);
					$transportRestriction->update(
						true,
						$shortTermTransportRestriction->getTitle(),
						$shortTermTransportRestriction->getDescription(),
						$shortTermTransportRestriction->getLink(),
						$shortTermTransportRestriction->getPublishedDate(),
						null,
						null,
						TransportRestrictionPriority::LEVEL_1,
						$shortTermTransportRestriction->getLines(),
						$this->datetimeFactory->createNow()
					);
				} catch (NoEntityFoundException $e) {
					$transportRestriction = $this->transportRestrictionFactory->createFromShortTermPidLibrary(
						$shortTermTransportRestriction
					);
					$this->entityManager->persist($transportRestriction);
				}

				if (array_key_exists($transportRestriction->getRestrictionId(), $currentlyActiveRestrictions)) {
					unset($currentlyActiveRestrictions[$transportRestriction->getRestrictionId()]);
				}
			}

			/** @var TransportRestriction $currentlyActiveRestriction */
			foreach ($currentlyActiveRestrictions as $currentlyActiveRestriction) {
				$currentlyActiveRestriction->disable($this->datetimeFactory->createNow());
			}

			$this->entityManager->flush();
			$this->entityManager->commit();
		} catch (Throwable $e) {
			$this->entityManager->rollback();
			throw $e;
		}

		$this->logger->info('Short term transport restrictions successfully processed');
	}

	public function importLongTerm(): void
	{
		$this->logger->info('Importing long term restrictions from pid library');

		$this->entityManager->beginTransaction();
		try {
			$longTermRestrictions = $this->rssService->getLongTermRestrictions();
			$currentlyActiveRestrictions = $this->transportRestrictionRepository->findActiveByType(
				TransportRestrictionType::LONG_TERM
			);

			$this->logger->info('Successfully fetched long term restrictions', [
				'count' => count($longTermRestrictions),
				'currentlyActiveRestriction' => count($currentlyActiveRestrictions),
			]);

			foreach ($longTermRestrictions as $longTermRestriction) {
				try {
					$transportRestriction = $this->transportRestrictionRepository->getTransportRestrictionId(
						$longTermRestriction->getGuid()
					);

					$dateTo = null;
					if ($longTermRestriction->getDateToTimestamp() !== null) {
						$dateTo = $this->datetimeFactory->createFromTimestamp($longTermRestriction->getDateToTimestamp());
					}

					$transportRestriction->update(
						true,
						$longTermRestriction->getTitle(),
						$longTermRestriction->getDescription(),
						$longTermRestriction->getLink(),
						$longTermRestriction->getPublishedDate(),
						$this->datetimeFactory->createFromTimestamp($longTermRestriction->getDateFromTimestamp()),
						$dateTo,
						$longTermRestriction->getPriority(),
						$longTermRestriction->getLines(),
						$this->datetimeFactory->createNow()
					);
				} catch (NoEntityFoundException $e) {
					$transportRestriction = $this->transportRestrictionFactory->createFromLongTermPidLibrary($longTermRestriction);
					$this->entityManager->persist($transportRestriction);
				}

				if (array_key_exists($transportRestriction->getRestrictionId(), $currentlyActiveRestrictions)) {
					unset($currentlyActiveRestrictions[$transportRestriction->getRestrictionId()]);
				}
			}

			/** @var TransportRestriction $currentlyActiveRestriction */
			foreach ($currentlyActiveRestrictions as $currentlyActiveRestriction) {
				$currentlyActiveRestriction->disable($this->datetimeFactory->createNow());
			}

			$this->entityManager->flush();
			$this->entityManager->commit();
		} catch (Throwable $e) {
			$this->entityManager->rollback();
			throw $e;
		}

		$this->logger->info('Long term transport restrictions successfully processed');
	}
}
