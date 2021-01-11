<?php

declare(strict_types=1);

namespace App\UI\Front\Control\Datagrid\Column;

use App\UI\Front\Control\Datagrid\Filter\FilterText;
use App\UI\Front\Control\Datagrid\FrontDatagrid;
use Mistrfilda\Datetime\Types\DatetimeImmutable;
use Ramsey\Uuid\UuidInterface;

class ColumnText implements IColumn
{
	public const TEMPLATE_FILE = __DIR__ . '/templates/columnText.latte';

	protected FrontDatagrid $datagrid;

	protected string $label;

	protected string $column;

	/** @var callable|null */
	protected $getterMethod;

	public function __construct(
		FrontDatagrid $datagrid,
		string $label,
		string $column,
		?callable $getterMethod = null
	) {
		$this->datagrid = $datagrid;
		$this->label = $label;
		$this->column = $column;
		$this->getterMethod = $getterMethod;
	}

	public function getColumn(): string
	{
		return $this->column;
	}

	public function getLabel(): string
	{
		return $this->label;
	}

	public function getDatagrid(): FrontDatagrid
	{
		return $this->datagrid;
	}

	public function setFilterText(): FilterText
	{
		return $this->datagrid->setFilterText($this);
	}

	public function getGetterMethod(): ?callable
	{
		return $this->getterMethod;
	}

	public function getTemplate(): string
	{
		return self::TEMPLATE_FILE;
	}

	/**
	 * @param string|int|float|DatetimeImmutable|UuidInterface $value
	 */
	public function processValue($value): string
	{
		if ($value instanceof DatetimeImmutable) {
			throw new DatagridColumnException(
				sprintf('Datetime object passed to column %s, use addColumnDatetime instead', $this->column));
		}

		if ($value instanceof UuidInterface) {
			return $value->toString();
		}

		return (string) $value;
	}
}
