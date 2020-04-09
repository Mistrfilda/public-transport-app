<?php

declare(strict_types=1);

namespace App\Transport\Prague\Vehicle;

use App\Doctrine\BaseRepository;
use Doctrine\ORM\QueryBuilder;

class VehicleRepository extends BaseRepository
{
    /**
     * @return Vehicle[]
     */
    public function findAll(): array
    {
        return $this->doctrineRepository->findAll();
    }

    public function createQueryBuilder(): QueryBuilder
    {
        return $this->doctrineRepository->createQueryBuilder('vehicle');
    }
}
