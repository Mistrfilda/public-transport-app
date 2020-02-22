<?php

declare(strict_types=1);

namespace App\Transport\Prague\DepartureTable;

use App\Transport\DepartureTable\IDepartureTableStopLinesFactory;
use App\Transport\Prague\Stop\Stop;
use App\Transport\Prague\StopLine\StopLineFactory;
use Ramsey\Uuid\Uuid;

class DepartureTableStopLinesFactory implements IDepartureTableStopLinesFactory
{
    /** @var StopLineFactory */
    private $stopLineFactory;

    /** @var DepartureTableRepository */
    private $departureTableRepository;

    public function __construct(
        StopLineFactory $stopLineFactory,
        DepartureTableRepository $departureTableRepository
    ) {
        $this->stopLineFactory = $stopLineFactory;
        $this->departureTableRepository = $departureTableRepository;
    }

    public function getStopLines(string $departureTableId): array
    {
        $departureTable = $this->departureTableRepository->findByStopId(Uuid::fromString($departureTableId));

        /** @var Stop $stop */
        $stop = $departureTable->getStop();
        return $this->stopLineFactory->getStopLinesForStop($stop);
    }
}
