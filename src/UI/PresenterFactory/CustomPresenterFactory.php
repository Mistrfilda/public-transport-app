<?php

declare(strict_types=1);

namespace App\UI\PresenterFactory;

use Nette\Application\PresenterFactory;

class CustomPresenterFactory extends PresenterFactory
{
	private string $appDir;

	/** @var array<string, string> */
	private array $customMapping;

	/**
	 * CustomPresenterFactory constructor.
	 * @param array<string, string> $customMapping
	 */
	public function __construct(string $appDir, array $customMapping, ?callable $factory = null)
	{
		parent::__construct($factory);
		$this->customMapping = $customMapping;
		$this->appDir = $appDir;
	}

	public function formatPresenterClass(string $presenter): string
	{
		if (array_key_exists($presenter, $this->customMapping)) {
			return $this->customMapping[$presenter];
		}

		return parent::formatPresenterClass($presenter);
	}

	public function unformatPresenterClass(string $class): ?string
	{
		if (($search = array_search($class, $this->customMapping, true)) !== false) {
			return $search;
		}

		return parent::unformatPresenterClass($class);
	}
}
