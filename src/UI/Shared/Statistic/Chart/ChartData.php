<?php

declare(strict_types=1);

namespace App\UI\Shared\Statistic\Chart;

use JsonSerializable;

class ChartData implements JsonSerializable
{
	/** @var string */
	private $label;

	/** @var string */
	private $tooltipSuffix;

	/** @var bool */
	private $useBackgroundColors;

	/** @var array<int, string> */
	private $labels;

	/** @var array<int, int> */
	private $data;

	/** @var array<int, string> */
	private $backgroundColors;

	/** @var array<int, string> */
	private $borderColors;

	public function __construct(string $label, bool $useBackgroundColors = true, string $tooltipSuffix = '')
	{
		$this->label = $label;
		$this->labels = [];
		$this->data = [];
		$this->backgroundColors = [];
		$this->borderColors = [];
		$this->useBackgroundColors = $useBackgroundColors;
		$this->tooltipSuffix = $tooltipSuffix;
	}

	public function add(string $label, int $item): void
	{
		$this->labels[] = $label;
		$this->data[] = $item;

		if ($this->useBackgroundColors) {
			$this->generateColorsForItem();
		}
	}

	/**
	 * @return mixed[]
	 */
	public function jsonSerialize(): array
	{
		return [
			'labels' => $this->labels,
			'tooltipSuffix' => $this->tooltipSuffix,
			'datasets' => [
				'label' => $this->label,
				'data' => $this->data,
				'backgroundColors' => $this->backgroundColors,
				'borderColors' => $this->borderColors,
			],
		];
	}

	private function generateColorsForItem(): void
	{
		$r = random_int(1, 255);
		$g = random_int(1, 255);
		$b = random_int(1, 255);

		$this->backgroundColors[] = sprintf('rgba(%d, %d, %d, 0.2)', $r, $g, $b);
		$this->borderColors[] = sprintf('rgba(%d, %d, %d, 1)', $r, $g, $b);
	}
}
