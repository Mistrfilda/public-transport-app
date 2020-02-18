<?php

declare(strict_types=1);

namespace App\Transport\DepartureTable;

use App\Transport\Stop\IStop;
use App\Transport\StopLine\IStopLine;

interface IDepartureTable
{
    public function getStop(): IStop;

    /** @return IStopLine[] */
    public function getStopLines(): array;
}
