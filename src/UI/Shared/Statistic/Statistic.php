<?php

declare(strict_types=1);

namespace App\UI\Shared\Statistic;

use Nette\Utils\Strings;

class Statistic
{
	public const CONTEXTUAL_SUCCESS = 'success';

	public const CONTEXTUAL_PRIMARY = 'primary';

	public const CONTEXTUAL_DANGER = 'danger';

	public const CONTEXTUAL_WARNING = 'warning';

	public const CONTEXTUAL_INFO = 'info';

	public const CONTEXTUAL_SECONDARY = 'secondary';

	private string $contextualClass;

	private string $heading;

	private string $value;

	private string $icon;

	private string $border;

	private string $size;

	/**
	 * Statistic constructor.
	 */
	public function __construct(
		string $contextualClass,
		string $heading,
		string $value,
		string $icon,
		string $border = 'border-left-',
		string $size = 'col-xl-6 col-md-6'
	) {
		$this->contextualClass = $contextualClass;
		$this->heading = $heading;
		$this->value = $value;
		$this->icon = $icon;
		$this->border = $border;
		$this->size = $size;
	}

	public function getContextualClass(): string
	{
		return $this->contextualClass;
	}

	public function getHeading(): string
	{
		return $this->heading;
	}

	public function getValue(bool $useTruncate = true): string
	{
		if ($useTruncate) {
			return Strings::truncate($this->value, 22);
		}

		return $this->value;
	}

	public function getIcon(): string
	{
		return $this->icon;
	}

	public function getBorder(): string
	{
		return $this->border . $this->getContextualClass();
	}

	public function getSize(): string
	{
		return $this->size;
	}
}
