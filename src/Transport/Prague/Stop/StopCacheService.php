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

    /** @var Cache */
    private $cache;

    /** @var StopRepository */
    private $stopRepository;

    /** @var string[] */
    private $stopPairs;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        IStorage $storage,
        StopRepository $stopRepository,
        LoggerInterface $logger
    ) {
        $this->cache = new Cache($storage, 'stops');
        $this->stopRepository = $stopRepository;
        $this->logger = $logger;
        $this->loadCache();
    }

    public function getStop(string $stopId): string
    {
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

    private function loadCache(bool $refresh = false): void
    {
        $cachedStops = $this->cache->load(self::STOP_PAIRS_KEY);
        if ($cachedStops !== null && $refresh === false) {
            $this->stopPairs = $cachedStops;
            return;
        }

        $this->stopPairs = $this->stopRepository->findStopIdPairs();
        $this->cache->save(self::STOP_PAIRS_KEY, $this->stopPairs);
    }
}
