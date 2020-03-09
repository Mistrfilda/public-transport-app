<?php

declare(strict_types=1);

namespace App\Transport\Prague\Statistic\Command;

use App\Transport\Prague\Statistic\TripStatisticFacade;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateTripStatisticCommand extends Command
{
    /** @var TripStatisticFacade */
    private $tripStatisticFacade;

    public function __construct(TripStatisticFacade $tripStatisticFacade)
    {
        parent::__construct(null);
        $this->tripStatisticFacade = $tripStatisticFacade;
    }

    public function configure(): void
    {
        parent::configure();
        $this->setName('prague:statistic:generate');
        $this->addArgument(
            'numberOfDays',
            InputArgument::REQUIRED,
            'Number of days to download'
        );
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $numberOfDays = $input->getArgument('numberOfDays');

        if (is_string($numberOfDays) === false) {
            throw new InvalidArgumentException();
        }

        $this->tripStatisticFacade->processStatistics((int) $numberOfDays);
        return 0;
    }
}
