<?php

declare(strict_types=1);

namespace App\UI\Admin\Base;

class AdminDatagridFactory
{
    public function create(): AdminDatagrid
    {
        return new AdminDatagrid();
    }
}
