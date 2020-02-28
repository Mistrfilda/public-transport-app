<?php

declare(strict_types=1);

namespace App\Request;

use App\Doctrine\BaseRepository;
use App\Doctrine\NoEntityFoundException;
use Doctrine\ORM\QueryBuilder;

class RequestRepository extends BaseRepository
{
    public function findById(int $id): Request
    {
        /** @var Request|null $request */
        $request = $this->doctrineRepository->findOneBy(['id' => $id]);

        if ($request === null) {
            throw new NoEntityFoundException();
        }

        return $request;
    }

    public function createQueryBuilder(): QueryBuilder
    {
        return $this->doctrineRepository->createQueryBuilder('request');
    }
}
