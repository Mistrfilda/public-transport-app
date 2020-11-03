<?php

declare(strict_types=1);

namespace App\Transport\Prague\Request\RabbitMQ\ParkingLot;

use App\Request\Request;
use App\Request\RequestRepository;
use App\Transport\Prague\Parking\Import\ParkingLotImportFacade;
use App\Utils\Datetime\DatetimeFactory;
use App\Utils\MonologHelper;
use Bunny\Message;
use Contributte\RabbitMQ\Consumer\IConsumer;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Schema\Expect;
use Nette\Schema\Processor;
use Nette\Utils\Json;
use Psr\Log\LoggerInterface;
use Throwable;

class ParkingLotConsumer implements IConsumer
{
	private LoggerInterface $logger;

	private DatetimeFactory $datetimeFactory;

	private ParkingLotImportFacade $parkingLotImportFacade;

	private EntityManagerInterface $entityManager;

	private RequestRepository $requestRepository;

	public function __construct(
		LoggerInterface $logger,
		DatetimeFactory $datetimeFactory,
		EntityManagerInterface $entityManager,
		RequestRepository $requestRepository,
		ParkingLotImportFacade $parkingLotImportFacade
	) {
		$this->logger = $logger;
		$this->datetimeFactory = $datetimeFactory;
		$this->parkingLotImportFacade = $parkingLotImportFacade;
		$this->entityManager = $entityManager;
		$this->requestRepository = $requestRepository;
	}

	public function consume(Message $message): int
	{
		/** @var Request|null $request */
		$request = null;
		try {
			$messageContents = Json::decode($message->content, Json::FORCE_ARRAY);
			$this->logger->info('Proccesing parking lot request', $messageContents);
			$this->validateMessageContents($messageContents);

			$request = $this->requestRepository->findById($messageContents['requestId']);

			$this->parkingLotImportFacade->import();

			$this->logger->info('Parking lot request request successfully finished', $messageContents);

			$request->finished($this->datetimeFactory->createNow());
			$this->entityManager->flush();
		} catch (Throwable $e) {
			if ($request !== null) {
				$request->failed($this->datetimeFactory->createNow());
				$this->entityManager->flush();
			}

			$this->logger->critical(MonologHelper::formatMessageFromException($e));
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
