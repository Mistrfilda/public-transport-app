<?php

declare(strict_types=1);

namespace App\Transport\Prague\Request\RabbitMQ\TransportRestriction;

use App\Request\Request;
use App\Request\RequestRepository;
use App\Transport\Prague\TransportRestriction\Import\TransportRestrictionImportFacade;
use App\Utils\MonologHelper;
use Bunny\Message;
use Contributte\RabbitMQ\Consumer\IConsumer;
use Doctrine\ORM\EntityManagerInterface;
use Mistrfilda\Datetime\DatetimeFactory;
use Nette\Schema\Expect;
use Nette\Schema\Processor;
use Nette\Utils\Json;
use Psr\Log\LoggerInterface;
use Throwable;

class TransportRestrictionConsumer implements IConsumer
{
	private LoggerInterface $logger;

	private DatetimeFactory $datetimeFactory;

	private TransportRestrictionImportFacade $transportRestrictionImportFacade;

	private EntityManagerInterface $entityManager;

	private RequestRepository $requestRepository;

	public function __construct(
		LoggerInterface $logger,
		DatetimeFactory $datetimeFactory,
		EntityManagerInterface $entityManager,
		RequestRepository $requestRepository,
		TransportRestrictionImportFacade $transportRestrictionImportFacade
	) {
		$this->logger = $logger;
		$this->datetimeFactory = $datetimeFactory;
		$this->transportRestrictionImportFacade = $transportRestrictionImportFacade;
		$this->entityManager = $entityManager;
		$this->requestRepository = $requestRepository;
	}

	public function consume(Message $message): int
	{
		/** @var Request|null $request */
		$request = null;
		try {
			$messageContents = Json::decode($message->content, Json::FORCE_ARRAY);
			$this->logger->info('Proccesing transport restriction request', $messageContents);
			$this->validateMessageContents($messageContents);

			$request = $this->requestRepository->findById($messageContents['requestId']);

			$this->transportRestrictionImportFacade->import();

			$this->logger->info('Transport restriction  request request successfully finished', $messageContents);

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
