<?php

declare(strict_types=1);

namespace App\UI\Admin\Base;

use App\Utils\SelectPicker;
use Ublaboo\DataGrid\Column\FilterableColumn;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\Filter\FilterSelect;

class AdminDatagrid extends DataGrid
{
    /**
     * @param array<int|string, string> $options
     */
    public function setFilterSelect(FilterableColumn $column, array $options): FilterSelect
    {
        $filter = $column->setFilterSelect($options);
        $filter->addAttribute('class', SelectPicker::BOOTSTRAP_SELECTPICKER);
        $filter->setPrompt('--Select--');
        return $filter;
    }
}
