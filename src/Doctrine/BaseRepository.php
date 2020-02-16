<?php

declare(strict_types=1);

namespace App\Doctrine;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

abstract class BaseRepository
{
    /** @var EntityManagerInterface */
    protected $entityManager;

    /** @var EntityRepository */
    protected $doctrineRepository;

    public function __construct(string $class, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        $repository = $entityManager->getRepository($class);

        if (! $repository instanceof EntityRepository) {
            throw new DBALException('Invalid entity repository!');
        }

        $this->doctrineRepository = $repository;
    }

    abstract public function createQueryBuilder(): QueryBuilder;
}
