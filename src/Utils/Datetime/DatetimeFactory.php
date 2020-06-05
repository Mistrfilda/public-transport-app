<?php

declare(strict_types=1);

namespace App\Utils\Datetime;

use DateTimeImmutable;

class DatetimeFactory
{
	public const DEFAULT_DATETIME_FORMAT = 'Y-m-d H:i:s';

	public const DEPARTURE_TABLE_DATETIME_FORMAT = 'd. m. Y H:i:s';

	public const DEFAULT_DATE_FORMAT = 'Y-m-d';

	public const DEFAULT_NULL_DATETIME_PLACEHOLDER = '---';

	public const DEFAULT_MYSQL_DATETIME_FORMAT = 'Y-m-d H:i:s';

	public function createNow(): DateTimeImmutable
	{
		return new DateTimeImmutable();
	}

	public function createToday(): DateTimeImmutable
	{
		return (new DateTimeImmutable())->setTime(0, 0, 0);
	}

	public function createDatetimeFromMysqlFormat(
		string $datetime,
		string $mysqlDatetimeFormat = self::DEFAULT_MYSQL_DATETIME_FORMAT
	): DateTimeImmutable {
		$parsedDatetime = DateTimeImmutable::createFromFormat($mysqlDatetimeFormat, $datetime);
		if ($parsedDatetime === false) {
			throw new DatetimeException('Can\t create datetome from specified value and format');
		}

		return $parsedDatetime;
	}
}
