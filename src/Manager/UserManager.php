<?php

namespace App\Manager;

use App\Entity\User;
use App\Repository\UserRepository;

class UserManager
{
    public function __construct(
        private readonly UserRepository $userRepository
    ) {}

    public function save(User $user): void
    {
        // Automatic add ROLE_USER
        $user->setRoles($user->getRoles());
        $this->userRepository->save($user, true);
    }

    public function getAll(): array
    {
        return $this->userRepository->findAll();
    }
}
