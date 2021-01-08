<?php

declare(strict_types=1);

namespace App\UI\Front\Control\Datagrid;

use App\UI\Front\Control\Datagrid\Column\ColumnBadge;
use App\UI\Front\Control\Datagrid\Column\ColumnText;
use App\UI\Front\Control\Datagrid\Column\IColumn;
use App\UI\Front\Control\Datagrid\Datasource\IDataSource;
use App\UI\Front\Control\Datagrid\Filter\FilterText;
use App\UI\Front\Control\Datagrid\Filter\IFilter;
use App\UI\Front\Control\Datagrid\Pagination\Pagination;
use App\UI\Front\Control\Datagrid\Pagination\PaginationService;
use Doctrine\Common\Collections\ArrayCollection;
use Nette\Application\UI\Control;

class FrontDatagrid extends Control
{
	/** @persistent */
	public int $offset;

	/** @persistent */
	public int $limit;

	private IDataSource $datasource;

	/**
	 * @var ArrayCollection<int, IColumn>
	 */
	private ArrayCollection $columns;

	/**
	 * @var ArrayCollection<int, IFilter>
	 */
	private ArrayCollection $filters;

	private PaginationService $paginationService;

	public function __construct(IDataSource $datasource)
	{
		$this->datasource = $datasource;

		$this->setPagination();
		$this->paginationService = new PaginationService();
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

	public function handleChangePagination(int $offset, int $limit): void
	{
		$this->offset = $offset;
		$this->limit = $limit;
		$this->redrawGridData();
	}

	public function handleArrowLeft(): void
	{
		if ($this->offset !== 0) {
			$this->offset -= $this->limit;
		}
		$this->redrawGridData();
	}

	public function handleArrowRight(): void
	{
		if ($this->offset + $this->limit < $this->datasource->getCount()) {
			$this->offset += $this->limit;
		}
		$this->redrawGridData();
	}

	public function render(): void
	{
		$template = $this->createTemplate(FrontDatagridTemplate::class);

		$dataCount = $this->datasource->getCount();
		$data = $this->datasource->getData($this->offset, $this->limit);

		$template->filters = $this->filters;
		$template->columns = $this->columns;

		$template->pagination = new Pagination(
			$this->limit,
			$this->offset,
			$this->paginationService->getPagination(
				$this->offset,
				$this->limit,
				$dataCount
			)
		);

		$template->itemsCount = $dataCount;
		$template->items = $data;
		$template->datasource = $this->datasource;

		$template->setFile(__DIR__ . '/frontDatagrid.latte');
		$template->render();
	}

	private function setPagination(): void
	{
		$this->offset = 0;
		$this->limit = 10;
	}

	private function redrawGridData(): void
	{
		$this->redrawControl('items');
		$this->redrawControl('pagination');
	}
}
