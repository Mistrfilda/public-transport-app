<?php

declare(strict_types=1);

namespace App\UI\Front\Control\Datagrid;

use App\UI\Front\Control\Datagrid\Datasource\IDataSource;

class FrontDatagridFactory
{
	public function create(IDataSource $dataSource): FrontDatagrid
	{
		return new FrontDatagrid($dataSource);
	}
}
