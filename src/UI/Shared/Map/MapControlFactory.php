<?php

declare(strict_types=1);

namespace App\UI\Shared\Map;

interface MapControlFactory
{
    public function create(IMapObjectProvider $mapObjectProvider): MapControl;
}
