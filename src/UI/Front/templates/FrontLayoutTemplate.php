<?php

declare(strict_types = 1);


namespace App\UI\Front\templates;


use App\UI\Front\FrontPresenter;
use App\UI\Shared\Menu\MenuItem;


class FrontLayoutTemplate
{
	public \Nette\Security\User $user;
	public string $baseUrl;
	public string $basePath;
	public array $flashes;
	public FrontPresenter $control;
	public FrontPresenter $presenter;
	/** @var MenuItem[] */
	public array $menuItems;
}