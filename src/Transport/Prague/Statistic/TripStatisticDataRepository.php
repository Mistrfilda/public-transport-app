<?php

declare(strict_types=1);

namespace App\Transport\Prague\Statistic;

use App\Doctrine\BaseRepository;
use Doctrine\ORM\QueryBuilder;

class TripStatisticDataRepository extends BaseRepository
{
    public function createQueryBuilder(): QueryBuilder
    {
        return $this->doctrineRepository->createQueryBuilder('tripStatistic');
    }
}
