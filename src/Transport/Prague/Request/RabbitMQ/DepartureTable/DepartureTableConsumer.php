<?php

declare(strict_types=1);

namespace App\Transport\Prague\Request\RabbitMQ\DepartureTable;

use App\Request\Request;
use App\Request\RequestRepository;
use App\Transport\Prague\DepartureTable\DepartureTableStopLineFacade;
use App\Utils\MonologHelper;
use Bunny\Message;
use Contributte\RabbitMQ\Consumer\IConsumer;
use Doctrine\ORM\EntityManagerInterface;
use Mistrfilda\Datetime\DatetimeFactory;
use Nette\Schema\Expect;
use Nette\Schema\Processor;
use Nette\Utils\Json;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use Throwable;

class DepartureTableConsumer implements IConsumer
{
	private LoggerInterface $logger;

	private RequestRepository $requestRepository;

	private EntityManagerInterface $entityManager;

	private DatetimeFactory $datetimeFactory;

	private DepartureTableStopLineFacade $departureTableStopLineFacade;

	public function __construct(
		LoggerInterface $logger,
		RequestRepository $requestRepository,
		EntityManagerInterface $entityManager,
		DatetimeFactory $datetimeFactory,
		DepartureTableStopLineFacade $departureTableStopLineFacade
	) {
		$this->logger = $logger;
		$this->requestRepository = $requestRepository;
		$this->entityManager = $entityManager;
		$this->datetimeFactory = $datetimeFactory;
		$this->departureTableStopLineFacade = $departureTableStopLineFacade;
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

			$this->departureTableStopLineFacade->downloadStoplinesForDepartureTable(
				Uuid::fromString($messageContents['departureTableId'])
			);

			$this->logger->info('Departure table request successfully finished', $messageContents);

			$request->finished($this->datetimeFactory->createNow());
			$this->entityManager->flush();
			$this->entityManager->clear();
		} catch (Throwable $e) {
			if ($request !== null) {
				$request->failed($this->datetimeFactory->createNow());
				$this->entityManager->flush();
				$this->entityManager->clear();
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
			'departureTableId' => Expect::string(),
			'dateTimestamp' => Expect::int(),
		]);

		(new Processor())->process($schema, $messageContents);
	}
}
