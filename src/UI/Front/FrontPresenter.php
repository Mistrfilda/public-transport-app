<?php

declare(strict_types=1);

namespace App\UI\Front;

use App\UI\Front\Menu\FrontMenuBuilder;
use App\UI\Shared\BasePresenter;

abstract class FrontPresenter extends BasePresenter
{
	/**
	 * @return string[]
	 */
	public function formatLayoutTemplateFiles(): array
	{
		return array_merge([__DIR__ . '/templates/@layout.latte'], parent::formatLayoutTemplateFiles());
	}

	public function beforeRender(): void
	{
		$this->template->menuItems = (new FrontMenuBuilder())->buildMenu();
		parent::beforeRender();
	}
}
