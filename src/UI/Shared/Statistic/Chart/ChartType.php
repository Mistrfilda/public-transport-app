<?php

declare(strict_types=1);

namespace App\UI\Shared\Statistic\Chart;

class ChartType
{
    public const LINE = 'line';

    public const BAR = 'bar';

    public static function typeExists(string $type): void
    {
        if (! in_array($type, self::getAll(), true)) {
            throw new ChartException('Invalid chart type');
        }
    }

    /**
     * @return string[]
     */
    public static function getAll(): array
    {
        return [
            self::LINE,
            self::BAR,
        ];
    }
}
