<?php

declare(strict_types=1);

namespace App\UI\Admin\Request;

use App\UI\Admin\AdminPresenter;
use App\UI\Admin\Base\AdminDatagrid;
use App\UI\Admin\Request\Datagrid\RequestDatagridFactory;

class RequestPresenter extends AdminPresenter
{
    /** @var RequestDatagridFactory */
    private $requestDatagridFactory;

    public function __construct(RequestDatagridFactory $requestDatagridFactory)
    {
        parent::__construct();
        $this->requestDatagridFactory = $requestDatagridFactory;
    }

    public function createComponentRequestGrid(): AdminDatagrid
    {
        return $this->requestDatagridFactory->create();
    }
}
