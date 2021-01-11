<?php

declare(strict_types=1);

namespace App\UI\Front\Statistic;

use App\UI\Front\FrontPresenter;
use App\UI\Front\Statistic\Control\Trip\TripStatisticControl;
use App\UI\Front\Statistic\Control\Trip\TripStatisticControlFactory;

class StatisticDetailPresenter extends FrontPresenter
{
	private TripStatisticControlFactory $tripStatisticControlFactory;

	public function __construct(TripStatisticControlFactory $tripStatisticControlFactory)
	{
		parent::__construct();
		$this->tripStatisticControlFactory = $tripStatisticControlFactory;
	}

	public function renderDefault(string $tripId): void
	{
	}

	protected function createComponentTripStatisticControl(): TripStatisticControl
	{
		$tripId = $this->processParameterStringId('tripId');
		return $this->tripStatisticControlFactory->create($tripId);
	}
}
