<?php

declare(strict_types=1);

namespace App\UI\Front\Control\Datagrid\Datasource;

use App\Doctrine\IEntity;
use App\UI\Front\Control\Datagrid\Column\IColumn;

interface IDataSource
{
	/** @return array<string|int, IEntity> */
	public function fetch(): array;

	public function getValueForColumn(IColumn $column, IEntity $row): string;
}
