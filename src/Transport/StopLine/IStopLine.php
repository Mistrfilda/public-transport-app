<?php

declare(strict_types=1);

namespace App\Transport\StopLine;

use App\Transport\Stop\IStop;
use App\Transport\Vehicle\IVehicle;
use DateTimeImmutable;

interface IStopLine
{
    public function getStop(): IStop;

    public function getArrivalTime(): DateTimeImmutable;

    public function getDepartureTime(): DateTimeImmutable;

    public function getTripId(): string;

    public function getLineNumber(): string;

    public function getFinalDestination(): string;

    /** Nullable, vehicle can be at depot! */
    public function getVehicle(): ?IVehicle;

    public function hasVehicle(): bool;
}
