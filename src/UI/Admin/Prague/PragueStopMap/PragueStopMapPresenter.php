<?php

declare(strict_types=1);

namespace App\UI\Admin\Prague\PragueStopMap;

use App\Transport\Prague\Stop\StopMapObjectProvider;
use App\UI\Admin\AdminPresenter;
use App\UI\Shared\Map\MapControl;
use App\UI\Shared\Map\MapControlFactory;

class PragueStopMapPresenter extends AdminPresenter
{
	private MapControlFactory $mapControlFactory;

	private StopMapObjectProvider $stopMapObjectProvider;

	public function __construct(
		MapControlFactory $mapControlFactory,
		StopMapObjectProvider $stopMapObjectProvider
	) {
		parent::__construct();
		$this->mapControlFactory = $mapControlFactory;
		$this->stopMapObjectProvider = $stopMapObjectProvider;
	}

	protected function createComponentStopMapControl(): MapControl
	{
		return $this->mapControlFactory->create($this->stopMapObjectProvider);
	}
}
