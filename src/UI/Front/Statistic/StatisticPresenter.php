<?php

declare(strict_types=1);

namespace App\UI\Front\Statistic;

use App\UI\Front\Base\InvalidArgumentException;
use App\UI\Front\FrontPresenter;
use App\UI\Front\Statistic\Control\Main\MainStatisticControl;
use App\UI\Front\Statistic\Control\Main\MainStatisticControlFactory;
use App\UI\Front\Statistic\Control\System\SystemStatisticControl;
use App\UI\Front\Statistic\Control\System\SystemStatisticControlFactory;
use App\UI\Front\Statistic\Control\Trip\TripStatisticControl;
use App\UI\Front\Statistic\Control\Trip\TripStatisticControlFactory;

class StatisticPresenter extends FrontPresenter
{
	private MainStatisticControlFactory $mainStatisticControlFactory;

	private TripStatisticControlFactory $tripStatisticControlFactory;

	private SystemStatisticControlFactory $systemStatisticControlFactory;

	public function __construct(
		MainStatisticControlFactory $mainStatisticControlFactory,
		TripStatisticControlFactory $tripStatisticControlFactory,
		SystemStatisticControlFactory $systemStatisticControlFactory
	) {
		parent::__construct();
		$this->mainStatisticControlFactory = $mainStatisticControlFactory;
		$this->tripStatisticControlFactory = $tripStatisticControlFactory;
		$this->systemStatisticControlFactory = $systemStatisticControlFactory;
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

	protected function createComponentStatisticControl(): SystemStatisticControl
	{
		return $this->systemStatisticControlFactory->create();
	}
}
