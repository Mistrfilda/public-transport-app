<?php

declare(strict_types=1);

namespace App\Admin;

use App\Doctrine\NoEntityFoundException;
use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\Identity;
use Nette\Security\IIdentity;
use Nette\Security\Passwords;

class AppAdminAuthenticator implements IAuthenticator
{
    /** @var AppAdminRepository */
    private $appAdminRepository;

    /** @var Passwords */
    private $passwords;

    public function __construct(AppAdminRepository $appAdminRepository, Passwords $passwords)
    {
        $this->appAdminRepository = $appAdminRepository;
        $this->passwords = $passwords;
    }

	/**
	 * @param string[] $credentials
	 * @return IIdentity
	 * @throws AuthenticationException
	 */
    public function authenticate(array $credentials): IIdentity
    {
        [$username, $password] = $credentials;

        try {
            $user = $this->appAdminRepository->findByUsername($username);
        } catch (NoEntityFoundException $e) {
            throw new AuthenticationException('User not found');
        }

        if (! $this->passwords->verify($password, $user->getPassword())) {
            throw new AuthenticationException('Invalid password');
        }

        return new Identity($user->getId());
    }
}
