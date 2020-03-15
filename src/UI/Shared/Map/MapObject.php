<?php

declare(strict_types=1);

namespace App\UI\Shared\Map;

use App\Utils\Coordinates;
use JsonSerializable;

class MapObject implements JsonSerializable
{
    /** @var Coordinates */
    private $coordinates;

    /** @var string */
    private $label;

    public function __construct(
        Coordinates $coordinates,
        string $label
    ) {
        $this->coordinates = $coordinates;
        $this->label = $label;
    }

    public function getCoordinates(): Coordinates
    {
        return $this->coordinates;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return mixed[]
     */
    public function jsonSerialize(): array
    {
        return [
            'coordinates' => $this->getCoordinates(),
            'label' => $this->getLabel(),
        ];
    }
}
