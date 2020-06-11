<?php

declare(strict_types=1);

namespace App\Utils\Datetime;

use DateTimeImmutable;

class CzechHoliday
{
	private string $name;

	private DateTimeImmutable $date;

	public function __construct(
		string $name,
		DateTimeImmutable $date
	) {
		$this->name = $name;
		$this->date = $date->setTime(0, 0, 0, 0);
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getDate(): DateTimeImmutable
	{
		return $this->date;
	}

	public function getTimestamp(): int
	{
		return $this->date->getTimestamp();
	}
}
