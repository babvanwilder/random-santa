<?php

namespace App\Command\Santa;

use App\Command\AppCommand;
use App\Entity\Participate;
use App\Entity\User;
use App\Manager\ParticipateManager;
use App\Manager\SantaManager;
use App\Manager\UserManager;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand(
    name: 'app:santa:participate-all',
    description: 'Add all user to a random santa',
)]
class SantaParticipateAllCommand extends AppCommand
{
    public function __construct(
        private readonly SantaManager $santaManager,
        private readonly UserManager  $userManager,
        private readonly ParticipateManager $participateManager,
        string                        $name = null
    )
    {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->addArgument('santaId', InputArgument::REQUIRED, 'random Santa Id')
        ;
    }

    /**
     * @throws Exception
     */
    protected function exec(): int
    {
        $santaId = (int)$this->input->getArgument('santaId');
        $santa = $this->santaManager->getOne($santaId);

        if (null === $santa) {
            throw new Exception("Santa id '{$santaId}' doesn't exist");
        }

        if (null !== $santa->getDateArchived()) {
            throw new Exception("This santa '{$santaId}' is archived");
        }

        /** @var Participate $participate */
        foreach ($santa->getParticipates() as $participate) {
            if (null !== $participate->getReceiver()) {
                throw new Exception("This santa '{$santaId}' is already calculate");
            }
        }

        /** @var User $user */
        foreach ($this->userManager->getAll() as $user) {
            $participate = $this->participateManager->getBySantaAndGiver($santa, $user);

            if (null === $participate) {
                $participate = (new Participate())->setGiver($user)->setSanta($santa);
                $this->participateManager->save($participate);
            }
        }

        return self::SUCCESS;
    }
}
