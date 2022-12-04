<?php

namespace App\Manager;

use App\Entity\Participate;
use App\Entity\Santa;
use App\Repository\SantaRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Security;

class SantaManager
{
    public function __construct(
        private readonly SantaRepository $santaRepository,
        private readonly Security $security,
        private readonly ParticipateManager $participateManager,
        private LoggerInterface $logger
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

    public function save(Santa $santa): void
    {
        $this->santaRepository->save($santa, true);
    }

    public function getOne(int $id): ?Santa
    {
        return $this->santaRepository->findOneBy([
            'id' => $id
        ]);
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

    /**
     * @throws Exception
     */
    public function calculate(Santa $santa, int $attempt = 1): void
    {
        if ($attempt > 5 || $santa->getParticipates()->count() === 1) {
            throw new Exception('Unable to calculate this Random Santa');
        }

        $receivers = $santa->getParticipates()->map(function(Participate $participate) {
            return $participate->getGiver();
        })->toArray();
        shuffle($receivers);
        /** @var Participate $participate */
        foreach($santa->getParticipates() as $participate) {
            if (count($receivers) === 1 && $receivers[0] === $participate->getGiver()) {
                $this->calculate($santa, $attempt++);
            } else {
                while ($receivers[0] === $participate->getGiver()) {
                    shuffle($receivers);
                }

                $receiver = array_pop($receivers);
                $participate->setReceiver($receiver);
                $this->participateManager->save($participate);
            }
        }

        $this->logger->info("Santa '{$santa->getName()}' ({$santa->getId()}) calculate in {$attempt} attempt");
    }
}
