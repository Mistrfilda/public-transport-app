<?php

declare(strict_types=1);

namespace App\UI\Front\Control\Datagrid\Column;

use App\UI\Front\Control\Datagrid\FrontDatagrid;
use Mistrfilda\Datetime\Types\DatetimeImmutable;
use Ramsey\Uuid\UuidInterface;

interface IColumn
{
	public function getDatagrid(): FrontDatagrid;

	public function getLabel(): string;

	public function getColumn(): string;

	public function getTemplate(): string;

	public function getGetterMethod(): ?callable;

	/**
	 * @param string|int|float|DatetimeImmutable|UuidInterface $value
	 */
	public function processValue($value): string;
}
