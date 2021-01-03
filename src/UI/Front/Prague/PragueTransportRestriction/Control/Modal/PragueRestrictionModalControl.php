<?php

declare(strict_types=1);

namespace App\UI\Front\Prague\PragueTransportRestriction\Control\Modal;

use App\Transport\Prague\TransportRestriction\TransportRestrictionRepository;
use App\UI\Front\Control\Modal\FrontModalControl;
use Ramsey\Uuid\UuidInterface;

class PragueRestrictionModalControl extends FrontModalControl
{
	private UuidInterface $transportRestrictionId;

	private TransportRestrictionRepository $transportRestrictionRepository;

	public function __construct(
		TransportRestrictionRepository $transportRestrictionRepository
	) {
		parent::__construct();
		$this->transportRestrictionRepository = $transportRestrictionRepository;
	}

	public function setTransportRestrictionId(UuidInterface $transportRestrictionId): void
	{
		$this->transportRestrictionId = $transportRestrictionId;
	}

	public function render(): void
	{
		$this->getTemplate()->transportRestriction = $this->transportRestrictionRepository->getById(
			$this->transportRestrictionId
		);
		$this->setTemplateFile(str_replace('.php', '.latte', __FILE__));
		parent::render();
	}
}
