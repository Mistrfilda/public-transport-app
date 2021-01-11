<?php

declare(strict_types=1);

namespace App\UI\Front\Control\Datagrid\Column;

use App\UI\Front\Control\Datagrid\FrontDatagrid;
use Mistrfilda\Datetime\DatetimeFactory;
use Mistrfilda\Datetime\Types\DatetimeImmutable;
use Ramsey\Uuid\UuidInterface;

class ColumnDatetime extends ColumnText
{
	public const DEFAULT_FORMAT = DatetimeFactory::DEFAULT_DATETIME_FORMAT;

	public const TEMPLATE_FILE = __DIR__ . '/templates/columnBadge.latte';

	private string $datetimeFormat = self::DEFAULT_FORMAT;

	public function __construct(
		FrontDatagrid $datagrid,
		string $label,
		string $column,
		?callable $getterMethod = null
	) {
		parent::__construct($datagrid, $label, $column, $getterMethod);
	}

	public function setFormat(string $datetimeFormat): self
	{
		$this->datetimeFormat = $datetimeFormat;
		return $this;
	}

	/**
	 * @param string|int|float|DatetimeImmutable|UuidInterface $value
	 */
	public function processValue($value): string
	{
		if ($value instanceof DatetimeImmutable) {
			return $value->format($this->datetimeFormat);
		}

		throw new DatagridColumnException('Invalid column type used');
	}
}
