<?php

declare(strict_types=1);

namespace App\UI\Front\Control\Chart;

use App\UI\Front\Base\BaseControl;
use App\UI\Shared\Statistic\Chart\ChartType;
use App\UI\Shared\Statistic\Chart\IChartDataProvider;
use Nette\Application\Responses\JsonResponse;
use Nette\Utils\Random;

class FrontChartControl extends BaseControl
{
	private string $type;

	private IChartDataProvider $chartDataProvider;

	public function __construct(
		string $type,
		IChartDataProvider $chartDataProvider
	) {
		ChartType::typeExists($type);
		$this->type = $type;
		$this->chartDataProvider = $chartDataProvider;
	}

	public function render(): void
	{
		$template = $this->createTemplate(FrontChartControlTemplate::class);

		$template->chartId = $this->getChartId();
		$template->chartType = $this->type;
		$template->setFile(str_replace('.php', '.latte', __FILE__));
		$template->render();
	}

	public function handleGetChartData(): void
	{
		$response = new JsonResponse($this->chartDataProvider->getChartData());
		$this->getPresenter()->sendResponse($response);
	}

	private function getChartId(): string
	{
		return Random::generate(12);
	}
}
