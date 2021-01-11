<?php

declare(strict_types=1);

namespace App\UI\Front\Control\Datagrid\Action;

class DatagridActionParameter
{
	private string $parameter;

	private string $referencedColumn;

	public function __construct(string $parameter, string $referencedColumn)
	{
		$this->parameter = $parameter;
		$this->referencedColumn = $referencedColumn;
	}

	public function getParameter(): string
	{
		return $this->parameter;
	}

	public function getReferencedColumn(): string
	{
		return $this->referencedColumn;
	}
}
