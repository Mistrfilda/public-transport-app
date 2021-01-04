<?php

declare(strict_types=1);

namespace App\UI\Front\Prague\PragueTransportRestriction\Control\Modal;

use App\Transport\Prague\TransportRestriction\TransportRestriction;
use App\UI\Front\Prague\PragueTransportRestriction\PragueTransportRestrictionPresenter;
use Nette\Bridges\ApplicationLatte\Template;
use Nette\Utils\IHtmlString;

/**
 * @method bool isLinkCurrent(string $destination = null, $args = [])
 * @method bool isModuleCurrent(string $module)
 */
class PragueRestrictionModalTemplate extends Template
{
	public PragueRestrictionModalControl $control;

	public PragueTransportRestrictionPresenter $presenter;

	public TransportRestriction $transportRestriction;

	public string $modalId;

	public ?IHtmlString $heading;

	public ?IHtmlString $content;

	/** @var array<string, mixed> */
	public array $additionalParameters;

	public string $originalTemplateFile;
}
