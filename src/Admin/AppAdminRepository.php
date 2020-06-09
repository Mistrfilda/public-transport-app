<?php

declare(strict_types=1);

namespace App\Admin;

use App\Doctrine\BaseRepository;
use App\Doctrine\NoEntityFoundException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Ramsey\Uuid\UuidInterface;

/**
 * @extends BaseRepository<AppAdmin>
 */
class AppAdminRepository extends BaseRepository
{
	public function findById(UuidInterface $appAdminId): AppAdmin
	{
		$qb = $this->createQueryBuilder();

		$qb->where($qb->expr()->eq('appAdmin.id', ':id'));
		$qb->setParameter('id', $appAdminId);

		try {
			return $qb->getQuery()->getSingleResult();
		} catch (NoResultException $e) {
			throw new NoEntityFoundException();
		}
	}

	public function findByUsername(string $username): AppAdmin
	{
		$qb = $this->createQueryBuilder();

		$qb->where($qb->expr()->eq('appAdmin.username', ':username'));
		$qb->setParameter('username', $username);

		try {
			return $qb->getQuery()->getSingleResult();
		} catch (NoResultException $e) {
			throw new NoEntityFoundException();
		}
	}

	public function createQueryBuilder(): QueryBuilder
	{
		return $this->doctrineRepository->createQueryBuilder('appAdmin');
	}
}
