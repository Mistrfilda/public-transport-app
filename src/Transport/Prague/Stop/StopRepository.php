<?php

declare(strict_types=1);

namespace App\Transport\Prague\Stop;

use App\Doctrine\BaseRepository;
use App\Doctrine\NoEntityFoundException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;

class StopRepository extends BaseRepository
{
    public function findById(int $id): Stop
    {
        /** @var Stop|null $stop */
        $stop = $this->doctrineRepository->findOneBy(['id' => $id]);

        if ($stop === null) {
            throw new NoEntityFoundException();
        }

        return $stop;
    }

    public function findByStopId(string $stopId): Stop
    {
        $qb = $this->createQueryBuilder();

        $qb->where($qb->expr()->eq('stop.stopId', ':stopId'));
        $qb->setParameter('stopId', $stopId);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            throw new NoEntityFoundException();
        }
    }

    /**
     * @return Stop[]
     */
    public function findAll(): array
    {
        return $this->doctrineRepository->findAll();
    }

    public function createQueryBuilder(): QueryBuilder
    {
        return $this->doctrineRepository->createQueryBuilder('stop');
    }
}
