<?php

declare(strict_types=1);

namespace App\UI\Front\Control\Datagrid\Datasource;

use App\Doctrine\IEntity;
use App\UI\Front\Control\Datagrid\Column\IColumn;
use Doctrine\ORM\QueryBuilder;
use Nette\Utils\Strings;

class DoctrineDataSource implements IDataSource
{
	private QueryBuilder $qb;

	public function __construct(QueryBuilder $qb)
	{
		$this->qb = $qb;
	}

	/** @return array<string|int, IEntity> */
	public function fetch(): array
	{
		return $this->qb->getQuery()->getResult();
	}

	public function getValueForColumn(IColumn $column, IEntity $row): string
	{
		if ($column->getGetterMethod() !== null) {
			return $column->getGetterMethod()($row);
		}

		$getterMethod = 'get' . Strings::firstUpper($column->getColumn());
		if (method_exists($row, $getterMethod) === false) {
			throw new DoctrineDataSourceException(
				sprintf(
					'Missing getter %s in entity %s',
					$getterMethod,
					get_class($row)
				)
			);
		}

		return (string) $row->{$getterMethod}();
	}
}
