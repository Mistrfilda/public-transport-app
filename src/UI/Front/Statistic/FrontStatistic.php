<?php

declare(strict_types=1);

namespace App\UI\Front\Statistic;

class FrontStatistic
{
	public const GREEN = 'bg-green-500';

	public const BLUE = 'bg-blue-500';

	public const RED = 'bg-red-500';

	public const YELLOW = 'bg-yellow-500';

	public const INDIGO = 'bg-indigo-500';

	public const TEAL = 'bg-teal-500';

	public const LIGHT_BLUE = 'bg-light-blue-500';

	private string $heading;

	private string $value;

	private string $icon;

	private string $color;

	public function __construct(
		string $heading,
		string $value,
		string $icon,
		string $color
	) {
		$this->heading = $heading;
		$this->value = $value;
		$this->icon = $icon;
		$this->color = $color;
	}

	public function getHeading(): string
	{
		return $this->heading;
	}

	public function getValue(): string
	{
		return $this->value;
	}

	public function getIcon(): string
	{
		return $this->icon;
	}

	public function getColor(): string
	{
		return $this->color;
	}
}
