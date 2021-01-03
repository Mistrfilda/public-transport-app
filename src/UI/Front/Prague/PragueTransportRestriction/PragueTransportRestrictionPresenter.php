<?php

declare(strict_types=1);

namespace App\UI\Front\Prague\PragueTransportRestriction;

use App\Transport\TransportRestriction\TransportRestrictionType;
use App\UI\Front\FrontPresenter;
use App\UI\Front\Prague\PragueTransportRestriction\Control\Modal\PragueRestrictionModalControl;
use App\UI\Front\Prague\PragueTransportRestriction\Control\Modal\PragueRestrictionModalControlFactory;
use App\UI\Front\Prague\PragueTransportRestriction\Control\PragueTransportRestrictionControl;
use App\UI\Front\Prague\PragueTransportRestriction\Control\PragueTransportRestrictionControlFactory;
use Ramsey\Uuid\Uuid;

class PragueTransportRestrictionPresenter extends FrontPresenter
{
	private PragueTransportRestrictionControlFactory $pragueTransportRestrictionControlFactory;

	private PragueRestrictionModalControlFactory $pragueRestrictionModalControlFactory;

	public function __construct(
		PragueTransportRestrictionControlFactory $pragueTransportRestrictionControlFactory,
		PragueRestrictionModalControlFactory $pragueRestrictionModalControlFactory
	) {
		parent::__construct();
		$this->pragueTransportRestrictionControlFactory = $pragueTransportRestrictionControlFactory;
		$this->pragueRestrictionModalControlFactory = $pragueRestrictionModalControlFactory;
	}

	public function handleShowTransportRestrictionModal(string $transportRestrictionId): void
	{
		$this['transportRestrictionModal']->setTransportRestrictionId(
			Uuid::fromString($transportRestrictionId)
		);
		$this->showModal(
			'transportRestrictionModal'
		);
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

	protected function createComponentTransportRestrictionModal(): PragueRestrictionModalControl
	{
		return $this->pragueRestrictionModalControlFactory->create();
	}
}
