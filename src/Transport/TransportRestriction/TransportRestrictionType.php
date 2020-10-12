<?php

declare(strict_types=1);

namespace App\Transport\TransportRestriction;

class TransportRestrictionType
{
	public const SHORT_TERM = 'short_term';

	public const LONG_TERM = 'long_term';

	public const ALL = [
		self::SHORT_TERM,
		self::LONG_TERM,
	];

	public static function exists(string $type): void
	{
		if (in_array($type, self::ALL, true) === false) {
			throw new TransportRestrictionTypeException();
		}
	}
}
