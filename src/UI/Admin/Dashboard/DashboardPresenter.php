<?php

declare(strict_types=1);

namespace App\UI\Admin\Dashboard;

use App\UI\Admin\AdminPresenter;
use App\UI\Shared\BasePresenter;
use App\UI\Shared\Statistic\Control\StatisticControl;
use App\UI\Shared\Statistic\Control\StatisticControlFactory;
use Nette\Utils\Html;

class DashboardPresenter extends AdminPresenter
{
    /** @var StatisticControlFactory */
    private $statisticControlFactory;

    public function __construct(
        StatisticControlFactory $statisticControlFactory
    ) {
        parent::__construct();
        $this->statisticControlFactory = $statisticControlFactory;
    }

    public function handleTestik(): void
    {
        $content = Html::el('p')->setText('takhle to jen vyzkousim co to udela :D');

        $this->showModal(
            BasePresenter::DEFAULT_MODAL_COMPONENT_NAME,
            'Thoel je jen a pouze test',
            $content
        );
    }

    protected function createComponentStatisticControl(): StatisticControl
    {
        return $this->statisticControlFactory->create();
    }
}
