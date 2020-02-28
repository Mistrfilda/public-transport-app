<?php

declare(strict_types=1);

namespace App\Request;

class RequestType
{
    public const PRAGUE_DEPARTURE_TABLE = 'prague_departure_table';

    public const PRAGUE_VEHICLE_POSITION = 'prague_vehicle_position';

    public static function validate(string $type): void
    {
        if (! in_array($type, self::getAll(), true)) {
            throw new RequestException('Invalid request type');
        }
    }

    /**
     * @return string[]
     */
    public static function getAll(): array
    {
        return [
            self::PRAGUE_DEPARTURE_TABLE,
            self::PRAGUE_VEHICLE_POSITION,
        ];
    }
}
