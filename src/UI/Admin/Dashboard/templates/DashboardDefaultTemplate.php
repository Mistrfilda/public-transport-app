<?php

declare(strict_types=1);

namespace App\UI\Admin\Dashboard\templates;

use App\Admin\AppAdmin;
use App\UI\Admin\Dashboard\DashboardPresenter;
use Nette\Security\User;

class DashboardDefaultTemplate
{
	public User $user;

	public string $baseUrl;

	public string $basePath;

	/** @var mixed[] */
	public array $flashes;

	public DashboardPresenter $control;

	public DashboardPresenter $presenter;

	public AppAdmin $appAdmin;
}
