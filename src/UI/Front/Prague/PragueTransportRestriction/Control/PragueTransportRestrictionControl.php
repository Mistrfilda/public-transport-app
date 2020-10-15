<?php

declare(strict_types=1);

namespace App\UI\Front\Prague\PragueTransportRestriction\Control;

use App\Transport\Prague\TransportRestriction\TransportRestriction;
use App\Transport\Prague\TransportRestriction\TransportRestrictionRepository;
use App\Transport\TransportRestriction\TransportRestrictionType;
use App\UI\Front\Base\BaseControl;

class PragueTransportRestrictionControl extends BaseControl
{
	private TransportRestrictionRepository $transportRestrictionRepository;

	private ?string $restrictionType = null;

	private string $cardGridColumn = 'col-md-6';

	public function __construct(TransportRestrictionRepository $transportRestrictionRepository)
	{
		$this->transportRestrictionRepository = $transportRestrictionRepository;
	}

	public function setRestrictionType(string $type): void
	{
		TransportRestrictionType::exists($type);
		$this->restrictionType = $type;
	}

	public function setCardGridColumn(string $cardGridColumn): void
	{
		$this->cardGridColumn = $cardGridColumn;
	}

	public function render(): void
	{
		$template = $this->getTemplate();
		$template->transportRestrictions = $this->getTransportRestrictions();
		$template->cardGridColumn = $this->cardGridColumn;
		$template->setFile(str_replace('.php', '.latte', __FILE__));
		$template->render();
	}

	/**
	 * @return TransportRestriction[]
	 */
	private function getTransportRestrictions(): array
	{
		if ($this->restrictionType !== null) {
			return $this->transportRestrictionRepository->findActiveByType($this->restrictionType);
		}

		return $this->transportRestrictionRepository->findActive();
	}
}
