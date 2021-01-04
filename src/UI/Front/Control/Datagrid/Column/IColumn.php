<?php

declare(strict_types=1);

namespace App\UI\Front\Control\Datagrid\Column;

use App\UI\Front\Control\Datagrid\FrontDatagrid;

interface IColumn
{
	public function getDatagrid(): FrontDatagrid;

	public function getLabel(): string;

	public function getColumn(): string;

	public function getTemplate(): string;

	public function getGetterMethod(): ?callable;
}
