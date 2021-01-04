<?php

declare(strict_types=1);

namespace App\UI\Front\Control\Datagrid\Column;

use App\UI\Front\Control\Datagrid\Filter\FilterText;
use App\UI\Front\Control\Datagrid\FrontDatagrid;

class ColumnText implements IColumn
{
	protected FrontDatagrid $datagrid;

	protected string $label;

	protected string $column;

	protected ?string $getterMethod;

	public function __construct(
		FrontDatagrid $datagrid,
		string $label,
		string $column,
		?string $getterMethod = null
	) {
		$this->datagrid = $datagrid;
		$this->label = $label;
		$this->column = $column;
		$this->getterMethod = $getterMethod;
	}

	public function getColumn(): string
	{
		return $this->column;
	}

	public function getLabel(): string
	{
		return $this->label;
	}

	public function getDatagrid(): FrontDatagrid
	{
		return $this->datagrid;
	}

	public function setFilterText(): FilterText
	{
	}

	public function getGetterMethod(): string
	{
		if ($this->getterMethod === null) {
			return $this->column;
		}

		return $this->getterMethod;
	}
}
