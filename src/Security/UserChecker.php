<?php

namespace App\Security;


use App\Entity\User;
use App\Exception\UserNotVerifiedException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function __construct(
        private LoggerInterface $logger
    ) { }

    public function checkPreAuth(UserInterface $user)
    {
        $this->logger->info(__METHOD__);

        if (!$user instanceof User) {
            return;
        }

        if (!$user->isVerified()) {
            $exception = new UserNotVerifiedException('Your account is not verified (checkPreAuth)');
            $exception->setUser($user);
            throw $exception;
        }
    }

    public function checkPostAuth(UserInterface $user)
    {
        $this->logger->info(__METHOD__);

        if (!$user instanceof User) {
            return;
        }

        if (!$user->isVerified()) {
            throw new AccessDeniedException('Your account is not verified');
        }
    }
}
