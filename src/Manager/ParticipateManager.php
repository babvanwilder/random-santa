<?php

namespace App\Manager;

use App\Entity\Participate;
use App\Entity\Santa;
use App\Entity\User;
use App\Repository\ParticipateRepository;
use Symfony\Component\Security\Core\Security;

class ParticipateManager
{
    public function __construct(
        private readonly ParticipateRepository $participateRepository,
        private readonly Security              $security
    ){}

    public function create(Santa $santa): Participate
    {
        $participate = (new Participate())
            ->setGiver($this->security->getUser())
            ->setSanta($santa);

        $this->participateRepository->save($participate, true);

        return $participate;
    }

    public function getBySantaAndGiver(Santa $santa, User $giver): ?Participate
    {
        return $this->participateRepository->findOneBy([
            'santa' => $santa,
            'giver' => $giver
        ]);
    }

    public function save(Participate $participate): void
    {
        $this->participateRepository->save($participate, true);
    }
}
