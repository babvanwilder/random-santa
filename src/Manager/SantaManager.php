<?php

namespace App\Manager;

use App\Entity\Santa;
use App\Repository\SantaRepository;
use DateTime;
use Symfony\Component\Security\Core\Security;

class SantaManager
{
    public function __construct(
        private readonly SantaRepository $santaRepository,
        private readonly Security $security
    ) {}

    public function create(string $name, int $years): Santa
    {
        $santa = (new Santa())
            ->setName($name)
            ->setYear($years)
            ->setClose(false)
            ->setOwner($this->security->getUser());

        $this->santaRepository->save($santa, true);

        return $santa;
    }

    public function close(Santa $santa): void
    {
        $santa->setClose(true)
            ->setDateClose(new DateTime())
            ->setCloser($this->security->getUser());

        $this->santaRepository->save($santa, true);
    }

    public function getAllOpen(): array
    {
        return $this->santaRepository->findBy([
            'close' => false
        ]);
    }

    public function getAllClosed(): array
    {
        return $this->santaRepository->findBy([
            'close' => true
        ]);
    }
}
