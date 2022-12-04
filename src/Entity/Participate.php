<?php

namespace App\Entity;

use App\Repository\ParticipateRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParticipateRepository::class)]
class Participate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $giver = null;

    #[ORM\ManyToOne]
    private ?User $receiver = null;

    #[ORM\ManyToOne(inversedBy: 'participates')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Santa $santa = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGiver(): ?User
    {
        return $this->giver;
    }

    public function setGiver(?User $giver): self
    {
        $this->giver = $giver;

        return $this;
    }

    public function getReceiver(): ?User
    {
        return $this->receiver;
    }

    public function setReceiver(?User $receiver): self
    {
        $this->receiver = $receiver;

        return $this;
    }

    public function getSanta(): ?Santa
    {
        return $this->santa;
    }

    public function setSanta(?Santa $santa): self
    {
        $this->santa = $santa;

        return $this;
    }
}
