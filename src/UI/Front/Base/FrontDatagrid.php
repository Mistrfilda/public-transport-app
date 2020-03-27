<?php

declare(strict_types=1);

namespace App\UI\Front\Base;

use App\Utils\DatetimeFactory;
use App\Utils\SelectPicker;
use DateTimeImmutable;
use Ublaboo\DataGrid\Column\ColumnDateTime;
use Ublaboo\DataGrid\Column\FilterableColumn;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\Filter\FilterSelect;

class FrontDatagrid extends DataGrid
{
    public const NULLABLE_PLACEHOLDER = '----';

    /**
     * @param array<int|string, string> $options
     */
    public function setFilterSelect(FilterableColumn $column, array $options): FilterSelect
    {
        $filter = $column->setFilterSelect($options);
        $filter->addAttribute('class', SelectPicker::BOOTSTRAP_SELECTPICKER);
        $filter->setPrompt(SelectPicker::PROMPT);
        return $filter;
    }

    public function addColumnDateTime(string $key, string $name, ?string $column = null): ColumnDateTime
    {
        $column = parent::addColumnDateTime($key, $name, $column);
        $column->setFormat(DatetimeFactory::DEFAULT_DATETIME_FORMAT);
        return $column;
    }

    public function addColumnDate(string $key, string $name, ?string $column = null): ColumnDateTime
    {
        $column = parent::addColumnDateTime($key, $name, $column);
        $column->setFormat(DatetimeFactory::DEFAULT_DATE_FORMAT);
        return $column;
    }

    public static function formatNullableDatetimeColumn(?DateTimeImmutable $time): string
    {
        if ($time === null) {
            return self::NULLABLE_PLACEHOLDER;
        }

        return $time->format(DatetimeFactory::DEFAULT_DATETIME_FORMAT);
    }
}
