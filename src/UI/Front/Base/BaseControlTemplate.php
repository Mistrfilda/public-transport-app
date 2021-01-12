<?php

declare(strict_types=1);

namespace App\UI\Front\Base;

use Nette\Application\UI\Presenter;
use Nette\Bridges\ApplicationLatte\Template;
use Nette\Security\User;
use stdClass;

class BaseControlTemplate extends Template
{
	public User $user;

	public string $baseUrl;

	public string $basePath;

	/** @var stdClass[] */
	public array $flashes;

	public Presenter $presenter;

	public BaseControl $control;
}
