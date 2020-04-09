<?php

declare(strict_types=1);

namespace App\Transport\Prague\Vehicle;

class VehicleType
{
	public const UNDEFINED = 0;

	public const CITY_BUS = 3;

	public const INTERCITY_BUS = 4;

	public const SCHOOL_BUS = 9;

	public const BUS_OTHER = 11;

	public const SHIP = 12;

	public const MIDI_BUS = 17;

	/** @var array<int, string> */
	private const ICONS = [
		self::CITY_BUS => 'fas fa-bus',
		self::INTERCITY_BUS => 'fas fa-bus',
		self::SCHOOL_BUS => 'fas fa-bus',
		self::BUS_OTHER => 'fas fa-bus',
		self::SHIP => 'fas fa-ship',
		self::MIDI_BUS => 'fas fa-bus',
	];

	/** @return int[] */
	public static function getAll(): array
	{
		return [
			self::UNDEFINED,
			self::CITY_BUS,
			self::INTERCITY_BUS,
			self::SCHOOL_BUS,
			self::BUS_OTHER,
			self::SHIP,
			self::MIDI_BUS,
		];
	}

	public static function getIcon(int $type): ?string
	{
		if (array_key_exists($type, self::ICONS)) {
			return self::ICONS[$type];
		}

		return null;
	}
}
