<?php

declare(strict_types=1);

namespace App\UI\Front\Prague\PragueTransportRestriction;

use App\Transport\TransportRestriction\TransportRestrictionType;
use App\UI\Front\FrontPresenter;
use App\UI\Front\Prague\PragueTransportRestriction\Control\PragueTransportRestrictionControl;
use App\UI\Front\Prague\PragueTransportRestriction\Control\PragueTransportRestrictionControlFactory;

class PragueTransportRestrictionPresenter extends FrontPresenter
{
	private PragueTransportRestrictionControlFactory $pragueTransportRestrictionControlFactory;

	public function __construct(
		PragueTransportRestrictionControlFactory $pragueTransportRestrictionControlFactory
	) {
		parent::__construct();
		$this->pragueTransportRestrictionControlFactory = $pragueTransportRestrictionControlFactory;
	}

	protected function createComponentShortTermRestrictionControl(): PragueTransportRestrictionControl
	{
		$control = $this->pragueTransportRestrictionControlFactory->create();
		$control->setRestrictionType(TransportRestrictionType::SHORT_TERM);
		return $control;
	}

	protected function createComponentLongTermRestrictionControl(): PragueTransportRestrictionControl
	{
		$control = $this->pragueTransportRestrictionControlFactory->create();
		$control->setRestrictionType(TransportRestrictionType::LONG_TERM);
		return $control;
	}
}
