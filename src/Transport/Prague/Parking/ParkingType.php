<?php

declare(strict_types=1);

namespace App\Transport\Prague\Parking;

class ParkingType
{
	public const PARK_AND_RIDE = '1';

	public const PAID_PARKING = '2';

	public const ALL = [
		self::PARK_AND_RIDE,
		self::PAID_PARKING,
	];

	public const LABELS = [
		self::PARK_AND_RIDE => 'P+R parkoviště',
		self::PAID_PARKING => 'Placené parkoviště',
	];

	public static function exists(string $type): bool
	{
		if (in_array($type, self::ALL, true)) {
			return true;
		}

		return false;
	}
}
