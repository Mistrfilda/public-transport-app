<?php

declare(strict_types=1);

namespace App\Transport\Prague\Request\RabbitMQ\VehiclePosition;

use App\Request\Request;
use App\Request\RequestRepository;
use App\Transport\Prague\Vehicle\Import\VehicleImportFacade;
use App\Utils\DatetimeFactory;
use Bunny\Message;
use Doctrine\ORM\EntityManagerInterface;
use Gamee\RabbitMQ\Consumer\IConsumer;
use Nette\Schema\Expect;
use Nette\Schema\Processor;
use Nette\Utils\Json;
use Psr\Log\LoggerInterface;
use Throwable;

class VehiclePositionConsumer implements IConsumer
{
    /** @var LoggerInterface */
    private $logger;

    /** @var DatetimeFactory */
    private $datetimeFactory;

    /** @var VehicleImportFacade */
    private $vehicleImportFacade;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var RequestRepository */
    private $requestRepository;

    public function __construct(
        LoggerInterface $logger,
        DatetimeFactory $datetimeFactory,
        VehicleImportFacade $vehicleImportFacade,
        EntityManagerInterface $entityManager,
        RequestRepository $requestRepository
    ) {
        $this->logger = $logger;
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
