<?php

declare(strict_types=1);

namespace App\Admin;

use Nette\Security\User;

class CurrentAppAdminGetter
{
    /** @var User */
    private $user;

    /** @var AppAdminRepository */
    private $appAdminRepository;

    public function __construct(User $user, AppAdminRepository $appAdminRepository)
    {
        $this->user = $user;
        $this->appAdminRepository = $appAdminRepository;
    }

    public function isLoggedIn(): bool
    {
        return $this->user->isLoggedIn();
    }

    public function getAppAdmin(): AppAdmin
    {
        if (! $this->isLoggedIn() || $this->user->getIdentity() === null) {
            throw new AppAdminNotLoggedInException();
        }

        return $this->appAdminRepository->findById($this->user->getIdentity()->getId());
    }

    public function login(string $username, string $password): void
    {
        $this->user->login($username, $password);
    }

    public function logout(): void
    {
        $this->user->logout(true);
    }
}
