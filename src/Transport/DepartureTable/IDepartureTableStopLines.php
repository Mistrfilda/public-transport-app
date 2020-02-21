<?php

declare(strict_types=1);

namespace App\Transport\DepartureTable;

use App\Transport\StopLine\IStopLine;

interface IDepartureTableStopLines
{
    /** @return IStopLine[] */
    public function getLines(): array;
}
