<?php

declare(strict_types=1);

namespace App\Utils;

use DateTimeImmutable;

class DatetimeFactory
{
    public const DEFAULT_DATETIME_FORMAT = 'Y-m-d H:i:s';

    public const DEPARTURE_TABLE_DATETIME_FORMAT = 'd. m. Y H:i:s';

    public const DEFAULT_DATE_FORMAT = 'Y-m-d';

    public const DEFAULT_NULL_DATETIME_PLACEHOLDER = '---';

    public function createNow(): DateTimeImmutable
    {
        return new DateTimeImmutable();
    }

    public function createToday(): DateTimeImmutable
    {
        return (new DateTimeImmutable())->setTime(0, 0, 0);
    }
}
