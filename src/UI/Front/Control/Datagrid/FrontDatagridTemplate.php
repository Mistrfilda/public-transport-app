<?php

declare(strict_types=1);

namespace App\UI\Front\Control\Datagrid;

use App\Doctrine\IEntity;
use App\UI\Front\Control\Datagrid\Action\IDatagridAction;
use App\UI\Front\Control\Datagrid\Column\IColumn;
use App\UI\Front\Control\Datagrid\Datasource\IDataSource;
use App\UI\Front\Control\Datagrid\Filter\IFilter;
use App\UI\Front\Control\Datagrid\Pagination\Pagination;
use Doctrine\Common\Collections\ArrayCollection;
use Nette\Application\UI\Presenter;
use Nette\Bridges\ApplicationLatte\Template;

class FrontDatagridTemplate extends Template
{
	public Presenter $presenter;

	public FrontDatagrid $control;

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

	/**
	 * @var ArrayCollection<int, IDatagridAction>
	 */
	public ArrayCollection $actions;

	public IDataSource $datasource;

	public Pagination $pagination;

	public int $itemsCount;
}
