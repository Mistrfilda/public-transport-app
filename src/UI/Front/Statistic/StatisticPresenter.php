<?php

declare(strict_types=1);

namespace App\UI\Front\Statistic;

use App\UI\Front\Base\InvalidArgumentException;
use App\UI\Front\FrontPresenter;
use App\UI\Front\Statistic\Control\Main\MainStatisticControl;
use App\UI\Front\Statistic\Control\Main\MainStatisticControlFactory;
use App\UI\Front\Statistic\Control\Trip\TripStatisticControl;
use App\UI\Front\Statistic\Control\Trip\TripStatisticControlFactory;
use App\UI\Shared\Statistic\Control\StatisticControl;
use App\UI\Shared\Statistic\Control\StatisticControlFactory;

class StatisticPresenter extends FrontPresenter
{
	private MainStatisticControlFactory $mainStatisticControlFactory;

	private TripStatisticControlFactory $tripStatisticControlFactory;

	private StatisticControlFactory $statisticControlFactory;

	public function __construct(
		MainStatisticControlFactory $mainStatisticControlFactory,
		TripStatisticControlFactory $tripStatisticControlFactory,
		StatisticControlFactory $statisticControlFactory
	) {
		parent::__construct();
		$this->mainStatisticControlFactory = $mainStatisticControlFactory;
		$this->tripStatisticControlFactory = $tripStatisticControlFactory;
		$this->statisticControlFactory = $statisticControlFactory;
	}

	public function renderTrip(string $tripId): void
	{
	}

	protected function createComponentMainStatisticControl(): MainStatisticControl
	{
		return $this->mainStatisticControlFactory->create();
	}

	protected function createComponentTripStatisticControl(): TripStatisticControl
	{
		$tripId = $this->getParameter('tripId');
		if ($tripId === null) {
			throw new InvalidArgumentException('Missing parameter tripId');
		}

		return $this->tripStatisticControlFactory->create($tripId);
	}

	protected function createComponentStatisticControl(): StatisticControl
	{
		$control = $this->statisticControlFactory->create();
		$control->setFrontTemplate();
		return $control;
	}
}
