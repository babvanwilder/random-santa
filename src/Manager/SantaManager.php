<?php

namespace App\Manager;

use App\Entity\Santa;
use App\Repository\SantaRepository;
use DateTimeImmutable;
use Symfony\Component\Security\Core\Security;

class SantaManager
{
    public function __construct(
        private readonly SantaRepository $santaRepository,
        private readonly Security $security
    ) {}

    public function create(string $name, DateTimeImmutable $dateStart, DateTimeImmutable $dateClose): Santa
    {
        $santa = (new Santa())
            ->setName($name)
            ->setDateStart($dateStart)
            ->setDateClose($dateClose)
            ->setOwner($this->security->getUser())
        ;

        $this->santaRepository->save($santa, true);

        return $santa;
    }

    public function archive(Santa $santa): void
    {
        $santa
            ->setDateArchived(new DateTimeImmutable())
            ->setArchiver($this->security->getUser())
        ;

        $this->santaRepository->save($santa, true);
    }

    public function getOpen(): array
    {
        return $this->santaRepository->findBy([
            'close' => false
        ]);
    }

    public function getClosed(string $interval): array
    {
        return $this->santaRepository->findBy([
            'close' => true

        ]);
    }
}
