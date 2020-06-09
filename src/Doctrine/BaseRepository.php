<?php

declare(strict_types=1);

namespace App\Doctrine;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * @template TEntityClass of object
 */
abstract class BaseRepository
{
	protected EntityManagerInterface $entityManager;

	/**
	 * @var EntityRepository<TEntityClass>
	 */
	protected EntityRepository $doctrineRepository;

	/**
	 * @template TClass
	 * @param class-string<TClass> $class
	 * @throws DBALException
	 */
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
