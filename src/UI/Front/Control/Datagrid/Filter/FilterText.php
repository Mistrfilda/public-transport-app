<?php

declare(strict_types=1);

namespace App\UI\Front\Control\Datagrid\Filter;

use App\UI\Front\Control\Datagrid\Column\ColumnText;

class FilterText implements IFilter
{
	public const TYPE = 'FILTER_TEXT';

	private ColumnText $column;

	private ?string $value;

	public function __construct(ColumnText $column)
	{
		$this->column = $column;
	}

	public function setValue(string $value): void
	{
		$this->value = $value;
	}

	public function getType(): string
	{
		return self::TYPE;
	}

	public function getColumn(): ColumnText
	{
		return $this->column;
	}

	/** @return mixed|null */
	public function getValue()
	{
		return $this->value;
	}
}
