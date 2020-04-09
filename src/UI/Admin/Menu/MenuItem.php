<?php

declare(strict_types=1);

namespace App\UI\Admin\Menu;

class MenuItem
{
	/** @var string */
	private $presenter;

	/** @var string */
	private $action;

	/** @var string */
	private $icon;

	/** @var string */
	private $label;

	/** @var MenuItem[] */
	private $childrens;

	/**
	 * MenuItem constructor.
	 * @param MenuItem[] $childrens
	 */
	public function __construct(string $presenter, string $action, string $icon, string $label, array $childrens = [])
	{
		$this->presenter = $presenter;
		$this->action = $action;
		$this->icon = $icon;
		$this->label = $label;
		$this->childrens = $childrens;
	}

	public function getPresenter(): string
	{
		return $this->presenter;
	}

	public function getAction(): string
	{
		return $this->action;
	}

	public function getLink(): string
	{
		return $this->presenter . ':' . $this->action;
	}

	public function getIcon(): string
	{
		return $this->icon;
	}

	public function getLabel(): string
	{
		return $this->label;
	}

	/**
	 * @return MenuItem[]
	 */
	public function getChildrens(): array
	{
		return $this->childrens;
	}

	public function isNested(): bool
	{
		return count($this->childrens) > 0;
	}

	/**
	 * @return string[]
	 */
	public function getActiveLinks(): array
	{
		$condition = [];
		return $this->getChildrenLinks($condition);
	}

	/**
	 * @param string[] $condition
	 * @return string[]
	 */
	private function getChildrenLinks(array &$condition): array
	{
		if ($this->presenter !== '') {
			$condition[] = $this->presenter . ':*';
		}

		if (count($this->childrens) > 0) {
			foreach ($this->childrens as $children) {
				$children->getChildrenLinks($condition);
			}
		}

		return $condition;
	}
}
