<?php

declare(strict_types=1);

namespace App\UI\Admin\Base;

class AdminDatagridFactory
{
    public function create(): AdminDatagrid
    {
        $grid = new AdminDatagrid();

        $grid::$iconPrefix = 'fa fa-';

        //GLOBAL PRESETS
        $grid->setRememberState(false);
        $grid->setOuterFilterRendering(true);
        $grid->setAutoSubmit(false);

        $grid->setTemplateFile(__DIR__ . '/templates/adminDatagrid.latte');

        return $grid;
    }
}
