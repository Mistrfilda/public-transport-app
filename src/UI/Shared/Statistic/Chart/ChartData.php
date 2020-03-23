<?php

declare(strict_types=1);

namespace App\UI\Shared\Statistic\Chart\LineChart;

use JsonSerializable;

class ChartData implements JsonSerializable
{
    /** @var string */
    private $label;

    /** @var array<int, string> */
    private $labels;

    /** @var array<int, int> */
    private $data;

    public function __construct(string $label)
    {
        $this->label = $label;
        $this->labels = [];
        $this->data = [];
    }

    public function add(string $label, int $item): void
    {
        $this->labels[] = $label;
        $this->data[] = $item;
    }

    /**
     * @return mixed[]
     */
    public function jsonSerialize(): array
    {
        return [
            'labels' => $this->labels,
            'datasets' => [
                'label' => $this->label,
                'data' => $this->data,
            ],
        ];
    }
}
