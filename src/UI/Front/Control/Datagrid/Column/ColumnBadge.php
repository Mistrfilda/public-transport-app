<?php

declare(strict_types=1);

namespace App\UI\Front\Control\Datagrid\Column;

use App\UI\Front\Control\Datagrid\FrontDatagrid;

class ColumnBadge extends ColumnText
{
	protected string $color;

	public function __construct(FrontDatagrid $datagrid, string $label, string $column, string $color)
	{
		parent::__construct($datagrid, $label, $column);
		$this->color = $color;
	}

	public function getColor(): string
	{
		return $this->color;
	}
}
