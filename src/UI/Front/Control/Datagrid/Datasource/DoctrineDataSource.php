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
	public function getData(int $offset, int $limit): array
	{
		return $this->qb
			->setFirstResult($offset)
			->setMaxResults($limit)
			->getQuery()
			->getResult();
	}

	public function getCount(): int
	{
		$countQb = clone $this->qb;

		return (int) $countQb
			->select('count(:rootAlias)')
			->setParameter('rootAlias', sprintf('%s.*', $this->getRootAlias()))
			->getQuery()
			->getSingleScalarResult();
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

		//@phpstan-ignore-next-line
		return (string) $row->{$getterMethod}();
	}

	private function getRootAlias(): string
	{
		$rootAliases = $this->qb->getRootAliases();
		if (array_key_exists(0, $rootAliases)) {
			return $rootAliases[0];
		}

		throw new DoctrineDataSourceException('Root alias not found');
	}
}
