<?php

declare(strict_types=1);

namespace App\Request;

use InvalidArgumentException;
use Nette\Utils\Json;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateRequestsCommand extends Command
{
    /** @var IRequestFacade[] */
    private $requestFacades;

    /** @var LoggerInterface */
    private $logger;

    /**
     * @param IRequestFacade[] $requestFacades
     */
    public function __construct(
        array $requestFacades,
        LoggerInterface $logger
    ) {
        parent::__construct(null);
        $this->requestFacades = $requestFacades;
        $this->logger = $logger;
    }

    public function configure(): void
    {
        parent::configure();
        $this->setName('requests:generate');
        $this->addArgument('conditions', InputArgument::REQUIRED, 'Conditions JSON');
        $this->addArgument('parameters', InputArgument::REQUIRED, 'Parameters JSON');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $conditions = $input->getArgument('conditions');
        $parameters = $input->getArgument('parameters');

        if (is_string($conditions) === false || is_string($parameters) === false) {
            throw new InvalidArgumentException();
        }

        $requestConditions = new RequestConditions(
            Json::decode($conditions, Json::FORCE_ARRAY),
            Json::decode($parameters, Json::FORCE_ARRAY)
        );

        $this->logger->debug('Generating all request', $requestConditions->jsonSerialize());
        foreach ($this->requestFacades as $requestFacade) {
            $requestFacade->generateRequests($requestConditions);
        }

        return 0;
    }
}
