<?php

declare(strict_types=1);

namespace App\Transport\Prague\Statistic\TripList;

use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Psr\Log\LoggerInterface;

class TripListCacheService
{
	private const TRIP_LIST_KEY = 'tripList';

	private Cache $cache;

	private TripListRepository $tripListRepository;

	/** @var array<string, int> */
	private array $tripListPairs;

	private LoggerInterface $logger;

	private bool $cacheLoaded = false;

	public function __construct(
		IStorage $storage,
		TripListRepository $tripListRepository,
		LoggerInterface $logger
	) {
		$this->cache = new Cache($storage, 'tripList');
		$this->tripListRepository = $tripListRepository;
		$this->logger = $logger;
	}

	public function getTripListId(string $tripId): int
	{
		$this->loadCache();
		if (array_key_exists($tripId, $this->tripListPairs)) {
			return $this->tripListPairs[$tripId];
		}

		$this->logger->warning('Can\'t find trip list in the cache', ['tripId' => $tripId]);
		throw new TripListException('Can\'t find trip list in the cache');
	}

	public function hasTripList(string $tripId): bool
	{
		$this->loadCache();
		return array_key_exists($tripId, $this->tripListPairs);
	}

	public function loadCache(bool $refresh = false): void
	{
		if ($this->cacheLoaded && $refresh === false) {
			return;
		}

		$cachedTripList = $this->cache->load(self::TRIP_LIST_KEY);
		$this->cacheLoaded = true;
		if ($cachedTripList !== null && $refresh === false) {
			$this->tripListPairs = $cachedTripList;
			return;
		}

		$this->tripListPairs = $this->tripListRepository->findTripIdPairs();
		$this->cache->save(self::TRIP_LIST_KEY, $this->tripListPairs);
	}
}
