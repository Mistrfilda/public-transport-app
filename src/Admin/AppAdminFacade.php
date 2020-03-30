<?php

declare(strict_types=1);

namespace App\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Nette\Security\Passwords;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\UuidInterface;

class AppAdminFacade
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var AppAdminRepository */
    private $appAdminRepository;

    /** @var Passwords */
    private $passwords;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        EntityManagerInterface $entityManager,
        AppAdminRepository $appAdminRepository,
        Passwords $passwords,
        LoggerInterface $logger
    ) {
        $this->entityManager = $entityManager;
        $this->appAdminRepository = $appAdminRepository;
        $this->passwords = $passwords;
        $this->logger = $logger;
    }

    public function createAppAdmin(
        string $name,
        string $username,
        string $email,
        string $password
    ): AppAdmin {
        $this->logger->info(
            'Creating app admin',
            [
                'name' => $name,
                'username' => $username,
                'email' => $email,
            ]
        );

        $appAdmin = new AppAdmin(
            $name,
            $username,
            $email,
            $this->passwords->hash($password)
        );

        $this->entityManager->persist($appAdmin);
        $this->entityManager->flush();
        $this->entityManager->refresh($appAdmin);

        return $appAdmin;
    }

    public function updateAppAdmin(
        UuidInterface $appAdminId,
        string $name,
        string $password
    ): AppAdmin {
        $this->logger->info(
            'Updating app admin',
            [
                'appAdminId' => $appAdminId->toString(),
                'name' => $name,
                'password' => $password,
            ]
        );

        $appAdmin = $this->appAdminRepository->findById($appAdminId);
        $appAdmin->update(
            $name,
            $this->passwords->hash($password)
        );

        $this->entityManager->flush();
        $this->entityManager->refresh($appAdmin);

        return $appAdmin;
    }
}
