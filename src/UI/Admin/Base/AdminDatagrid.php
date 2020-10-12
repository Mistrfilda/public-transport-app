<?php

declare(strict_types=1);

namespace App\UI\Admin\Base;

use App\Utils\Datetime\DatetimeFactory;
use App\Utils\SelectPicker;
use DateTimeImmutable;
use Nette\Utils\Strings;
use Ublaboo\DataGrid\Column\ColumnDateTime;
use Ublaboo\DataGrid\Column\FilterableColumn;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\Filter\FilterSelect;

class AdminDatagrid extends DataGrid
{
	public const NULLABLE_PLACEHOLDER = '----';

	public const BOOL_OPTIONS = [
		null => 'Select',
		1 => 'Yes',
		0 => 'No',
	];

	public static function formatNullableDatetimeColumn(?DateTimeImmutable $time): string
	{
		if ($time === null) {
			return self::NULLABLE_PLACEHOLDER;
		}

		return $time->format(DatetimeFactory::DEFAULT_DATETIME_FORMAT);
	}

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

	public function render(): void
	{
		$this->getTemplate()->originalTemplatesFilePath = $this->getOriginalTemplatesFilePath();
		parent::render();
	}

	private function getOriginalTemplatesFilePath(): string
	{
		$filePath = $this->getOriginalTemplateFile();
		return Strings::substring($filePath, 0, Strings::indexOf($filePath, '/', -1)) . '/';
	}
}
