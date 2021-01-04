<?php

declare(strict_types=1);

namespace App\UI\Front\Control\Datagrid\Filter;

use App\UI\Front\Control\Datagrid\Column\IColumn;

interface IFilter
{
	public function getType(): string;

	public function getColumn(): IColumn;

	/** @return mixed|null */
	public function getValue();
}
