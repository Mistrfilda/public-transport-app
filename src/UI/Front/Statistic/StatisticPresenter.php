<?php

declare(strict_types=1);

namespace App\UI\Front\Statistic;

use App\UI\Front\Base\InvalidArgumentException;
use App\UI\Front\FrontPresenter;
use App\UI\Front\Statistic\Control\Trip\TripStatisticControl;
use App\UI\Front\Statistic\Control\Trip\TripStatisticControlFactory;

class StatisticPresenter extends FrontPresenter
{
    /** @var TripStatisticControlFactory */
    private $tripStatisticControlFactory;

    public function __construct(TripStatisticControlFactory $tripStatisticControlFactory)
    {
        parent::__construct();
        $this->tripStatisticControlFactory = $tripStatisticControlFactory;
    }

    public function renderTrip(string $tripId): void
    {
    }

    protected function createComponentTripStatisticControl(): TripStatisticControl
    {
        $tripId = $this->getParameter('tripId');
        if ($tripId === null) {
            throw new InvalidArgumentException('Missing parameter tripId');
        }

        return $this->tripStatisticControlFactory->create($tripId);
    }
}
