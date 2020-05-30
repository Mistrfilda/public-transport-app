<?php

declare(strict_types=1);

namespace App\Transport\Prague\Request\RabbitMQ\DepartureTable;

use App\Request\Request;
use App\Request\RequestRepository;
use App\Transport\Prague\DepartureTable\DepartureTableRepository;
use App\Transport\Prague\StopLine\StopTime\Import\StopTimeImportFacade;
use App\Transport\Prague\StopLine\Trip\Import\TripImportFacade;
use App\Utils\DatetimeFactory;
use Bunny\Message;
use Contributte\RabbitMQ\Consumer\IConsumer;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Schema\Expect;
use Nette\Schema\Processor;
use Nette\Utils\Json;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use Throwable;
use Tracy\ILogger;

class DepartureTableConsumer implements IConsumer
{
	private LoggerInterface $logger;

	private ILogger $tracyLogger;

	private StopTimeImportFacade $stopTimeImportFacade;

	private TripImportFacade $tripImportFacade;

	private DepartureTableRepository $departureTableRepository;

	private RequestRepository $requestRepository;

	private EntityManagerInterface $entityManager;

	private DatetimeFactory $datetimeFactory;

	public function __construct(
		LoggerInterface $logger,
		ILogger $tracyLogger,
		StopTimeImportFacade $stopTimeImportFacade,
		TripImportFacade $tripImportFacade,
		DepartureTableRepository $departureTableRepository,
		RequestRepository $requestRepository,
		EntityManagerInterface $entityManager,
		DatetimeFactory $datetimeFactory
	) {
		$this->logger = $logger;
		$this->stopTimeImportFacade = $stopTimeImportFacade;
		$this->tripImportFacade = $tripImportFacade;
		$this->departureTableRepository = $departureTableRepository;
		$this->requestRepository = $requestRepository;
		$this->entityManager = $entityManager;
		$this->datetimeFactory = $datetimeFactory;
		$this->tracyLogger = $tracyLogger;
	}

	public function consume(Message $message): int
	{
		/** @var Request|null $request */
		$request = null;
		try {
			$messageContents = Json::decode($message->content, Json::FORCE_ARRAY);
			$this->logger->info('Proccesing departure table request', $messageContents);
			$this->validateMessageContents($messageContents);

			$request = $this->requestRepository->findById($messageContents['requestId']);

			$departureTable = $this->departureTableRepository->findById(
				Uuid::fromString($messageContents['departureTableId'])
			);

			$this->stopTimeImportFacade->import(
				$departureTable->getPragueStop()->getId(),
				$departureTable->getNumberOfFutureDays()
			);

			$this->tripImportFacade->import(
				$departureTable->getPragueStop()->getId(),
				$departureTable->getNumberOfFutureDays()
			);

			$this->logger->info('Departure table request successfully finished', $messageContents);

			$request->finished($this->datetimeFactory->createNow());
			$this->entityManager->flush();
		} catch (Throwable $e) {
			if ($request !== null) {
				$request->failed($this->datetimeFactory->createNow());
				$this->entityManager->flush();
			}

			$this->tracyLogger->log($e);
		}

		return IConsumer::MESSAGE_ACK;
	}

	/**
	 * @param array<string, string|int> $messageContents
	 */
	private function validateMessageContents(array $messageContents): void
	{
		$schema = Expect::structure([
			'requestId' => Expect::int(),
			'departureTableId' => Expect::string(),
			'dateTimestamp' => Expect::int(),
		]);

		(new Processor())->process($schema, $messageContents);
	}
}
