<?php

declare(strict_types=1);

namespace App\UI\Admin;

use App\Admin\CurrentAppAdminGetter;
use App\UI\Admin\Menu\AdminMenuBuilder;
use App\UI\Shared\BasePresenter;

abstract class AdminPresenter extends BasePresenter
{
	protected CurrentAppAdminGetter $currentAppAdminGetter;

	public function injectCurrentAppAdminGetter(CurrentAppAdminGetter $currentAppAdminGetter): void
	{
		$this->currentAppAdminGetter = $currentAppAdminGetter;
	}

	public function startup(): void
	{
		parent::startup();
		if ($this->currentAppAdminGetter->isLoggedIn() === false) {
			$this->redirect('Login:default', ['backlink' => $this->storeRequest()]);
		}

		$this->template->appAdmin = $this->currentAppAdminGetter->getAppAdmin();
		$this->template->menuItems = (new AdminMenuBuilder())->buildMenu();
	}

	/**
	 * @return string[]
	 */
	public function formatLayoutTemplateFiles(): array
	{
		return array_merge([__DIR__ . '/templates/@layout.latte'], parent::formatLayoutTemplateFiles());
	}

	public function handleLogout(): void
	{
		$this->currentAppAdminGetter->logout();
		$this->redirect('this');
	}
}
