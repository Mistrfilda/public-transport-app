<?php

declare(strict_types=1);

namespace App\Transport\Prague\Vehicle;

use App\Doctrine\BaseRepository;
use Doctrine\ORM\QueryBuilder;

class VehicleRepository extends BaseRepository
{
    public function createQueryBuilder(): QueryBuilder
    {
        return $this->doctrineRepository->createQueryBuilder('vehicle');
    }
}
