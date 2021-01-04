<?php

declare(strict_types=1);

namespace App\UI\Front\Control\Datagrid;

use App\Doctrine\IEntity;
use App\UI\Front\Base\BaseControlTemplate;
use App\UI\Front\Control\Datagrid\Column\IColumn;
use App\UI\Front\Control\Datagrid\Datasource\IDataSource;
use App\UI\Front\Control\Datagrid\Filter\IFilter;
use Doctrine\Common\Collections\ArrayCollection;

class FrontDatagridTemplate extends BaseControlTemplate
{
	/**
	 * @var ArrayCollection<int, IColumn>
	 */
	public ArrayCollection $columns;

	/**
	 * @var ArrayCollection<int, IFilter>
	 */
	public ArrayCollection $filters;

	/**
	 * @var array<int|string, IEntity>
	 */
	public array $items;

	public IDataSource $datasource;
}
