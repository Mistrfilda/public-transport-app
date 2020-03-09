<?php

declare(strict_types=1);

namespace App\Transport\Prague\Vehicle\Filter;

use App\Transport\Prague\Vehicle\VehicleType;

class VehicleTypeFilter
{
    public function format(int $vehicleTypeId): ?string
    {
        return VehicleType::getIcon($vehicleTypeId);
    }
}
