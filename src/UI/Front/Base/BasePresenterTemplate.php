<?php

declare(strict_types=1);

namespace App\UI\Front\Base;

use App\UI\Shared\Menu\MenuItem;
use Nette\Bridges\ApplicationLatte\Template;
use Nette\Security\User;
use stdClass;

class BasePresenterTemplate extends Template
{
	public User $user;

	public string $baseUrl;

	public string $basePath;

	/** @var stdClass[] */
	public array $flashes;

	/** @var MenuItem[] */
	public array $menuItems;
}
