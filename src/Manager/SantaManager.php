<?php

namespace App\Manager;

use App\Entity\Santa;
use App\Repository\SantaRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Symfony\Component\Security\Core\Security;

class SantaManager
{
    public function __construct(
        private readonly SantaRepository $santaRepository,
        private readonly Security $security
    ) {}

    public function create(string $name, DateTimeInterface $dateStart, DateTimeInterface $dateClose): Santa
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

    public function update(Santa $santa): void
    {
        $this->santaRepository->save($santa, true);
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
        return $this->santaRepository->findOpen();
    }

    public function getFuture(): array
    {
        return $this->santaRepository->findFuture();
    }

    public function getClosed(): array
    {
        return $this->santaRepository->findClose();
    }

    public function getArchived(): array
    {
        return $this->santaRepository->findArchived();
    }
}
