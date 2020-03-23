<?php

declare(strict_types=1);

namespace App\UI\Shared\Statistic\Chart\Control;

use App\UI\Shared\Statistic\Chart\ChartType;
use App\UI\Shared\Statistic\Chart\LineChart\IChartDataProvider;
use Nette\Application\Responses\JsonResponse;
use Nette\Application\UI\Control;
use Nette\Utils\Random;

class ChartControl extends Control
{
    /** @var string */
    private $type;

    /** @var IChartDataProvider */
    private $chartDataProvider;

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
        $this->getTemplate()->chartId = $this->getChartId();
        $this->getTemplate()->chartClass = $this->getChartClass();
        $this->getTemplate()->setFile(str_replace('.php', '.latte', __FILE__));
        $this->getTemplate()->render();
    }

    public function handleGetChartData(): void
    {
        $response = new JsonResponse($this->chartDataProvider->getChartData());
        $this->getPresenter()->sendResponse($response);
    }

    private function getChartClass(): string
    {
        return 'chart--' . $this->type;
    }

    private function getChartId(): string
    {
        return Random::generate(10);
    }
}
