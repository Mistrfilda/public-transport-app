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

	public function __construct(
		IStorage $storage,
		TripListRepository $tripListRepository,
		LoggerInterface $logger
	) {
		$this->cache = new Cache($storage, 'tripList');
		$this->tripListRepository = $tripListRepository;
		$this->logger = $logger;
		$this->loadCache();
	}

	public function getTripListId(string $tripId): int
	{
		if (array_key_exists($tripId, $this->tripListPairs)) {
			return $this->tripListPairs[$tripId];
		}

		$this->logger->warning('Can\'t find trip list in the cache', ['tripId' => $tripId]);
		throw new TripListException('Can\'t find trip list in the cache');
	}

	public function hasTripList(string $tripId): bool
	{
		return array_key_exists($tripId, $this->tripListPairs);
	}

	private function loadCache(bool $refresh = false): void
	{
		$cachedTripList = $this->cache->load(self::TRIP_LIST_KEY);
		if ($cachedTripList !== null && $refresh === false) {
			$this->tripListPairs = $cachedTripList;
			return;
		}

		$this->tripListPairs = $this->tripListRepository->findTripIdPairs();
		$this->cache->save(self::TRIP_LIST_KEY, $this->tripListPairs);
	}
}
