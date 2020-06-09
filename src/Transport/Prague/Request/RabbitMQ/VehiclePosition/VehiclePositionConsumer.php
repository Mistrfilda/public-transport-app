<?php

declare(strict_types=1);

namespace App\Transport\Prague\Request\RabbitMQ\VehiclePosition;

use App\Request\Request;
use App\Request\RequestRepository;
use App\Transport\Prague\Vehicle\Import\VehicleImportFacade;
use App\Utils\Datetime\DatetimeFactory;
use Bunny\Message;
use Contributte\RabbitMQ\Consumer\IConsumer;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Schema\Expect;
use Nette\Schema\Processor;
use Nette\Utils\Json;
use Psr\Log\LoggerInterface;
use Throwable;
use Tracy\ILogger;

class VehiclePositionConsumer implements IConsumer
{
	private LoggerInterface $logger;

	private ILogger $tracyLogger;

	private DatetimeFactory $datetimeFactory;

	private VehicleImportFacade $vehicleImportFacade;

	private EntityManagerInterface $entityManager;

	private RequestRepository $requestRepository;

	public function __construct(
		LoggerInterface $logger,
		ILogger $tracyLogger,
		DatetimeFactory $datetimeFactory,
		VehicleImportFacade $vehicleImportFacade,
		EntityManagerInterface $entityManager,
		RequestRepository $requestRepository
	) {
		$this->logger = $logger;
		$this->tracyLogger = $tracyLogger;
		$this->datetimeFactory = $datetimeFactory;
		$this->vehicleImportFacade = $vehicleImportFacade;
		$this->entityManager = $entityManager;
		$this->requestRepository = $requestRepository;
	}

	public function consume(Message $message): int
	{
		/** @var Request|null $request */
		$request = null;
		try {
			$messageContents = Json::decode($message->content, Json::FORCE_ARRAY);
			$this->logger->info('Proccesing vehicle position request', $messageContents);
			$this->validateMessageContents($messageContents);

			$request = $this->requestRepository->findById($messageContents['requestId']);

			$this->vehicleImportFacade->import();

			$this->logger->info('vehicle position request successfully finished', $messageContents);

			$request->finished($this->datetimeFactory->createNow());
			$this->entityManager->flush();
		} catch (Throwable $e) {
			if ($request !== null) {
				$request->failed($this->datetimeFactory->createNow());
				$this->entityManager->flush();
			}

			$this->tracyLogger->log($e, ILogger::CRITICAL);
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
			'dateTimestamp' => Expect::int(),
		]);

		(new Processor())->process($schema, $messageContents);
	}
}
