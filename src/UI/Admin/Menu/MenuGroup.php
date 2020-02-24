<?php

declare(strict_types=1);

namespace App\UI\Admin\Menu;

class MenuGroup
{
    /** @var string */
    private $label;

    /** @var bool */
    private $showLabel = true;

    /** @var MenuItem[] */
    private $menuItems;

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
