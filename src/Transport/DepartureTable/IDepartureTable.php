<?php

declare(strict_types=1);

namespace App\Transport\DepartureTable;

use App\Transport\Stop\IStop;

interface IDepartureTable
{
    public function getStop(): IStop;

    public function getDownloadNumberOfDays(): int;
}
