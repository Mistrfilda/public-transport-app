<?php

declare(strict_types=1);

namespace App\Transport\Prague\Vehicle;

use App\Doctrine\BaseRepository;
use App\Doctrine\OrderBy;
use Doctrine\ORM\QueryBuilder;

class VehiclePositionRepository extends BaseRepository
{
    public function createQueryBuilder(): QueryBuilder
    {
        return $this->doctrineRepository->createQueryBuilder('vehiclePosition');
    }

    public function findLast(): VehiclePosition
    {
        $qb = $this->createQueryBuilder();
        $qb->orderBy('vehiclePosition.createdAt', OrderBy::DESC);
        $qb->setMaxResults(1);
        return $qb->getQuery()->getSingleResult();
    }
}
