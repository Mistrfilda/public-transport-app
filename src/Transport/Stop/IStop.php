<?php

namespace App\Transport\Stop;

use App\Utils\Coordinates;

interface IStop
{
    public function getName(): string;

    public function getStopId(): string;

    public function getCoordinates(): Coordinates;
}
