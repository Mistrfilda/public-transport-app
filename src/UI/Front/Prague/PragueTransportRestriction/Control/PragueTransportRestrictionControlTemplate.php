<?php

declare(strict_types=1);

namespace App\UI\Front\Prague\PragueTransportRestriction\Control;

use App\Transport\Prague\TransportRestriction\TransportRestriction;
use App\UI\Front\Prague\PragueTransportRestriction\PragueTransportRestrictionPresenter;
use Nette\Security\User;

/**
 * @method bool isLinkCurrent(string $destination = null, $args = [])
 * @method bool isModuleCurrent(string $module)
 */
class PragueTransportRestrictionControlTemplate
{
	public User $user;

	public string $baseUrl;

	public string $basePath;

	/** @var string[] */
	public array $flashes;

	public PragueTransportRestrictionControl $control;

	public PragueTransportRestrictionPresenter $presenter;

	/** @var TransportRestriction[] */
	public array $transportRestrictions;
}
