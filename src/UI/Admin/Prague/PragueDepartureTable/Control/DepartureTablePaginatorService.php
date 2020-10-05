<?php

declare(strict_types=1);

namespace App\UI\Admin\Prague\PragueDepartureTable\Control;

use Nette\Http\Session;
use Nette\Http\SessionSection;
use Ramsey\Uuid\UuidInterface;

class DepartureTablePaginatorService
{
	private const SESSION_PREFIX = 'departure-table-';

	private const LOAD_STEP = 10;

	private SessionSection $sessionSection;

	private int $currentStep;

	private int $loadedCount;

	public function __construct(
		UuidInterface $departureTableId,
		Session $session
	) {
		$sectionName = self::SESSION_PREFIX . $departureTableId->toString();
		if (! $session->hasSection($sectionName)) {
			$this->sessionSection = $session->getSection($sectionName);
			$this->reset();
		} else {
			$this->sessionSection = $session->getSection($sectionName);
		}

		$this->sessionSection->setExpiration('2 minutes');
		$this->currentStep = $this->sessionSection->currentStep;
		$this->loadedCount = $this->sessionSection->loadedCount;
	}

	public function increase(): void
	{
		++$this->currentStep;
		$this->loadedCount += self::LOAD_STEP;

		$this->sessionSection->currentStep = $this->currentStep;
		$this->sessionSection->loadedCount = $this->loadedCount;
	}

	public function reset(): void
	{
		$this->sessionSection->currentStep = 1;
		$this->sessionSection->loadedCount = self::LOAD_STEP;
		$this->currentStep = $this->sessionSection->currentStep;
		$this->loadedCount = $this->sessionSection->loadedCount;
	}

	public function getCurrentStep(): int
	{
		return $this->currentStep;
	}

	public function getLoadedCount(): int
	{
		return $this->loadedCount;
	}
}
