<?php

declare(strict_types=1);

namespace App\UI\Shared\Menu;

class MenuGroup
{
	private string $label;

	private bool $showLabel = true;

	/** @var MenuItem[] */
	private array $menuItems;

	/**
	 * MenuGroup constructor.
	 * @param MenuItem[] $menuItems
	 */
	public function __construct(string $label, bool $showLabel, array $menuItems)
	{
		$this->label = $label;
		$this->showLabel = $showLabel;
		$this->menuItems = $menuItems;
	}

	public function getLabel(): string
	{
		return $this->label;
	}

	public function shouldShowLabel(): bool
	{
		return $this->showLabel;
	}

	/**
	 * @return MenuItem[]
	 */
	public function getMenuItems(): array
	{
		return $this->menuItems;
	}
}
