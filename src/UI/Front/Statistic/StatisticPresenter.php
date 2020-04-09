<?php

declare(strict_types=1);

namespace App\UI\Front\Statistic;

use App\UI\Front\Base\InvalidArgumentException;
use App\UI\Front\FrontPresenter;
use App\UI\Front\Statistic\Control\Main\MainStatisticControl;
use App\UI\Front\Statistic\Control\Main\MainStatisticControlFactory;
use App\UI\Front\Statistic\Control\Trip\TripStatisticControl;
use App\UI\Front\Statistic\Control\Trip\TripStatisticControlFactory;

class StatisticPresenter extends FrontPresenter
{
	/** @var MainStatisticControlFactory */
	private $mainStatisticControlFactory;

	/** @var TripStatisticControlFactory */
	private $tripStatisticControlFactory;

	public function __construct(
		MainStatisticControlFactory $mainStatisticControlFactory,
		TripStatisticControlFactory $tripStatisticControlFactory
	) {
		parent::__construct();
		$this->mainStatisticControlFactory = $mainStatisticControlFactory;
		$this->tripStatisticControlFactory = $tripStatisticControlFactory;
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
}
