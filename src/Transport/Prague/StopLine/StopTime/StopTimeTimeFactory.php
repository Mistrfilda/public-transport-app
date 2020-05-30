<?php

declare(strict_types=1);

namespace App\Transport\Prague\StopLine\StopTime;

use DateTimeImmutable;

class StopTimeTimeFactory
{
	public function createDatetime(DateTimeImmutable $date, string $apiTime): DateTimeImmutable
	{
		$apiTimeParts = explode(':', $apiTime);

		if (count($apiTimeParts) !== 3) {
			throw new InvalidTimeException();
		}

		$apiTimeParts = array_map(fn (string $value): int => (int) $value, $apiTimeParts);

		//API WTF? There are tiems like 24:55:00, 26:33:00
		if ($apiTimeParts[0] >= 24) {
			$apiTimeParts[0] -= 24;
		}

		return $date->setTime($apiTimeParts[0], $apiTimeParts[1], $apiTimeParts[2]);
	}
}
