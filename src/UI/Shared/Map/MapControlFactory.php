<?php

declare(strict_types=1);

namespace App\UI\Shared\Map;

interface MapControlFactory
{
    /**
     * @param MapObject[] $mapObjects
     */
    public function create(array $mapObjects): MapControl;
}
