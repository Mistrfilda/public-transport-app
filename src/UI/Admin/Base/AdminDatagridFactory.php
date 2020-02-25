<?php

declare(strict_types=1);

namespace App\UI\Admin\Base;

class AdminDatagridFactory
{
    public function create(): AdminDatagrid
    {
        $grid = new AdminDatagrid();

        $grid::$iconPrefix = '';

        //GLOBAL PRESETS
        $grid->setRememberState(false);
        $grid->setOuterFilterRendering(true);
        $grid->setAutoSubmit(false);

        return $grid;
    }
}
