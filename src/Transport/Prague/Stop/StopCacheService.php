<?php

declare(strict_types=1);

namespace App\Transport\Prague\Stop;

use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Psr\Log\LoggerInterface;

class StopCacheService
{
	public const UNDEFINED_STOP_PLACEHOLDER = '----';

	private const STOP_PAIRS_KEY = 'stopsByStopId';

	private Cache $cache;

	private StopRepository $stopRepository;

	/** @var string[] */
	private array $stopPairs;

	private LoggerInterface $logger;

	private bool $cacheLoaded = false;

	public function __construct(
		IStorage $storage,
		StopRepository $stopRepository,
		LoggerInterface $logger
	) {
		$this->cache = new Cache($storage, 'stops');
		$this->stopRepository = $stopRepository;
		$this->logger = $logger;
	}

	public function getStop(string $stopId): string
	{
		$this->loadCache();
		if (array_key_exists($stopId, $this->stopPairs)) {
			return $this->stopPairs[$stopId];
		}

		//Attemp to refresh cache to see if stop exists
		$this->loadCache(true);
		if (array_key_exists($stopId, $this->stopPairs)) {
			return $this->stopPairs[$stopId];
		}

		$this->logger->warning('Can\'t find stop by stop id in cache service', ['stopId' => $stopId]);

		return self::UNDEFINED_STOP_PLACEHOLDER;
	}

	public function loadCache(bool $refresh = false): void
	{
		if ($this->cacheLoaded && $refresh === false) {
			return;
		}

		$cachedStops = $this->cache->load(self::STOP_PAIRS_KEY);
		$this->cacheLoaded = true;
		if ($cachedStops !== null && $refresh === false) {
			$this->stopPairs = $cachedStops;
			return;
		}

		$this->stopPairs = $this->stopRepository->findStopIdPairs();
		$this->cache->save(self::STOP_PAIRS_KEY, $this->stopPairs);
	}
}
