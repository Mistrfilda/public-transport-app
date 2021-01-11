<?php

declare(strict_types=1);

namespace App\UI\Front\Control\Datagrid\Datasource;

use App\Doctrine\IEntity;
use App\UI\Front\Control\Datagrid\Column\IColumn;
use Ramsey\Uuid\UuidInterface;

interface IDataSource
{
	/** @return array<string|int, IEntity> */
	public function getData(int $offset, int $limit): array;

	public function getCount(): int;

	public function getValueForColumn(IColumn $column, IEntity $row): string;

	/** @return string|int|float|UuidInterface */
	public function getValueForKey(string $key, IEntity $row);
}
