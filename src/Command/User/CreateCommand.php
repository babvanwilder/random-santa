<?php

namespace App\Command\User;

use App\Command\AppCommand;
use App\Entity\User;
use App\Manager\UserManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:user:create',
    description: 'Add a short description for your command',
)]
class CreateCommand extends AppCommand
{
    public function __construct(
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly UserManager                 $userManager,
        string                                       $name = null
    )
    {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                'email',
                InputArgument::REQUIRED,
                'Email for the new User'
            )
            ->addArgument(
                'clearPassword',
                InputArgument::REQUIRED,
                'Clear password for the new User')
            ->addOption(
                'firstName',
                'f',
                InputOption::VALUE_REQUIRED,
                'First Name for the new User')
            ->addOption(
                'lastName',
                'l',
                InputOption::VALUE_REQUIRED,
                'Last Name for the new User')
        ;
    }

    protected function exec(): int
    {
        $user = (new User())
            ->setEmail($this->input->getArgument('email'))
            ->setFirstname($this->input->getOption('firstName'))
            ->setFirstname($this->input->getOption('firstName'))
            ->setLastname($this->input->getOption('lastName'))
            ->setIsVerified(true)
        ;

        $user->setPassword(
            $this->userPasswordHasher->hashPassword(
                $user,
                $this->input->getArgument('clearPassword')
            )
        );

        $this->userManager->save($user);

        return self::SUCCESS;
    }
}
