<?php

declare(strict_types=1);

namespace App\UI\Front\Homepage;

use App\Transport\Prague\DepartureTable\DepartureTableRepository;
use App\UI\Front\FrontPresenter;
use App\UI\Shared\Statistic\Control\StatisticControl;
use App\UI\Shared\Statistic\Control\StatisticControlFactory;

class HomepagePresenter extends FrontPresenter
{
    /** @var DepartureTableRepository */
    private $departureTableRepository;

    /** @var StatisticControlFactory */
    private $statisticControlFactory;

    public function __construct(
        DepartureTableRepository $departureTableRepository,
        StatisticControlFactory $statisticControlFactory
    ) {
        parent::__construct();
        $this->departureTableRepository = $departureTableRepository;
        $this->statisticControlFactory = $statisticControlFactory;
    }

    public function renderDefault(): void
    {
        $this->template->departureTables = $this->departureTableRepository->findAll();
    }

    protected function createComponentStatisticControl(): StatisticControl
    {
        return $this->statisticControlFactory->create();
    }
}
