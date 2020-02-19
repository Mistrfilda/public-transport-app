<?php

declare(strict_types=1);

namespace App\Utils;

use DateTimeImmutable;

class DatetimeFactory
{
    public const DEFAULT_DATETIME_FORMAT = 'Y-m-d H:i:s';

    public function createNow(): DateTimeImmutable
    {
        return new DateTimeImmutable();
    }

    public function createToday(): DateTimeImmutable
    {
        return (new DateTimeImmutable())->setTime(0, 0, 0);
    }
}
