<?php

declare(strict_types=1);

namespace App\Request;

class RequestType
{
	public const PRAGUE_DEPARTURE_TABLE = 'prague_departure_table';

	public const PRAGUE_VEHICLE_POSITION = 'prague_vehicle_position';

	public const PRAGUE_PARKING_LOT = 'prague_parking_lot';

	public const PRAGUE_TRANSPORT_RESTRICTION = 'prague_transport_restriction';

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
			self::PRAGUE_PARKING_LOT,
			self::PRAGUE_TRANSPORT_RESTRICTION,
		];
	}

	/**
	 * @return string[]
	 */
	public static function getLabels(): array
	{
		return [
			self::PRAGUE_VEHICLE_POSITION => 'Prague vehicle position',
			self::PRAGUE_DEPARTURE_TABLE => 'Prague departure table',
			self::PRAGUE_PARKING_LOT => 'Prague parking lot',
			self::PRAGUE_TRANSPORT_RESTRICTION => 'Prague transport restriction',
		];
	}
}
