<?php

declare(strict_types=1);

namespace App\Transport\DepartureTable;

use App\Transport\StopLine\IStopLine;

interface IDepartureTableStopLinesFactory
{
    /** @return IStopLine[] */
    public function getStopLines(string $departureTableId): array;
}
