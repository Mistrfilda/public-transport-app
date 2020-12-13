<?php

declare(strict_types=1);

namespace App\UI\Filter;

use App\Utils\Datetime\DatetimeConst;
use DateTimeImmutable;

class DatetimeFormatFilter
{
	public function format(?DateTimeImmutable $datetime): string
	{
		if ($datetime === null) {
			return DatetimeConst::DEFAULT_NULL_DATETIME_PLACEHOLDER;
		}

		return $datetime->format(DatetimeConst::DEPARTURE_TABLE_DATETIME_FORMAT);
	}
}
