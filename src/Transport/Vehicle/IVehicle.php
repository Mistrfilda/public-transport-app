<?php

declare(strict_types=1);

namespace App\Transport\Vehicle;

use App\Utils\Coordinates;

interface IVehicle
{
    public function getVehiclePosition(): IVehiclePosition;

    public function getRouteId(): string;

    public function getCoordinates(): Coordinates;

    public function getFinalStation(): string;

    public function getDelayInSeconds(): int;

    public function hasDelay(): bool;

    public function isWheelchairAccessible(): bool;

    public function getLastStopId(): ?string;

    public function getNextStopId(): ?string;

    public function getRegistrationNumber(): ?string;

    public function getVehicleType(): int;

    public function getTripId(): string;

    public function getCompany(): ?string;
}
