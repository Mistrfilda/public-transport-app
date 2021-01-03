<?php

declare(strict_types = 1);


namespace App\UI\Front\Prague\PragueTransportRestriction\Control\Modal;


use App\Transport\Prague\TransportRestriction\TransportRestriction;
use App\UI\Front\Prague\PragueTransportRestriction\PragueTransportRestrictionPresenter;
use Nette\Security\User;


/**
 * @method bool isLinkCurrent(string $destination = null, $args = [])
 * @method bool isModuleCurrent(string $module)
 */
class PragueRestrictionModalTemplate
{
	public PragueRestrictionModalControl $control;
	public PragueTransportRestrictionPresenter $presenter;
	public TransportRestriction $transportRestriction;
	public string $modalId;
	public $heading;
	public $content;
	public array $additionalParameters;
	public string $originalTemplateFile;
}
