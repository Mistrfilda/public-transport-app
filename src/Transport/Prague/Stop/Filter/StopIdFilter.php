<?php

declare(strict_types=1);

namespace App\Transport\Prague\Stop\Filter;

use App\Transport\Prague\Stop\StopCacheService;

class StopIdFilter
{
	/** @var StopCacheService */
	private $stopCacheService;

	public function __construct(StopCacheService $stopCacheService)
	{
		$this->stopCacheService = $stopCacheService;
	}

	public function format(?string $stopId): string
	{
		if ($stopId === null) {
			return StopCacheService::UNDEFINED_STOP_PLACEHOLDER;
		}

		return $this->stopCacheService->getStop($stopId);
	}
}
