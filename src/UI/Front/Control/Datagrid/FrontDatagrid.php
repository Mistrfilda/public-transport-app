<?php

declare(strict_types=1);

namespace App\UI\Front\Control\Datagrid;

use App\UI\Front\Control\Datagrid\Column\ColumnBadge;
use App\UI\Front\Control\Datagrid\Column\ColumnText;
use App\UI\Front\Control\Datagrid\Column\IColumn;
use App\UI\Front\Control\Datagrid\Datasource\IDataSource;
use App\UI\Front\Control\Datagrid\Filter\FilterText;
use App\UI\Front\Control\Datagrid\Filter\IFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Nette\Application\UI\Control;

class FrontDatagrid extends Control
{
	private IDataSource $datasource;

	/**
	 * @var ArrayCollection<int, IColumn>
	 */
	private ArrayCollection $columns;

	/**
	 * @var ArrayCollection<int, IFilter>
	 */
	private ArrayCollection $filters;

	public function __construct(IDataSource $datasource)
	{
		$this->datasource = $datasource;
		$this->columns = new ArrayCollection();
		$this->filters = new ArrayCollection();
	}

	public function addColumnText(
		string $label,
		string $column,
		?callable $getterMethod = null
	): ColumnText {
		$column = new ColumnText(
			$this,
			$label,
			$column,
			$getterMethod
		);
		$this->columns->add($column);
		return $column;
	}

	public function addColumnBadge(
		string $label,
		string $column,
		string $color,
		?callable $getterMethod = null,
		?callable $colorCallback = null
	): ColumnText {
		$column = new ColumnBadge(
			$this,
			$label,
			$column,
			$color,
			$getterMethod,
			$colorCallback
		);
		$this->columns->add($column);
		return $column;
	}

	public function setFilterText(ColumnText $column): FilterText
	{
		$filter = new FilterText($column);
		$this->filters->add($filter);
		return $filter;
	}

	public function render(): void
	{
		$template = $this->getTemplate();

		$template->filters = $this->filters;
		$template->columns = $this->columns;
		$template->items = $this->datasource->fetch();
		$template->datasource = $this->datasource;

		$template->setFile(__DIR__ . '/frontDatagrid.latte');
		$template->render();
	}
}
