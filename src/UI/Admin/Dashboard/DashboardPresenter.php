<?php

declare(strict_types=1);

namespace App\UI\Admin\Dashboard;

use App\UI\Admin\AdminPresenter;
use App\UI\Shared\Statistic\Control\StatisticControl;
use App\UI\Shared\Statistic\Control\StatisticControlFactory;

class DashboardPresenter extends AdminPresenter
{
    /** @var StatisticControlFactory */
    private $statisticControlFactory;

    public function __construct(StatisticControlFactory $statisticControlFactory)
    {
        parent::__construct();
        $this->statisticControlFactory = $statisticControlFactory;
    }

    protected function createComponentStatisticControl(): StatisticControl
    {
        return $this->statisticControlFactory->create();
    }
}
