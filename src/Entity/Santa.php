<?php

namespace App\Entity;

use App\Repository\SantaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SantaRepository::class)]
class Santa
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom ne doit pas être vide")]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Assert\NotBlank(message: "La date de début ne doit pas être vide")]
    #[Assert\LessThan(propertyPath: "dateClose", message: "La date de début doit être supérieur à la date de fin")]
    private ?\DateTimeInterface $dateStart = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Assert\NotBlank(message: "La date de fin ne doit pas être vide")]
    #[Assert\GreaterThan(propertyPath: "dateStart", message: "La date de fin doit être supérieur à la date de début")]
    private ?\DateTimeInterface $dateClose = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateArchived = null;

    #[ORM\ManyToOne]
    private ?User $archiver = null;

    #[ORM\OneToMany(mappedBy: 'santa', targetEntity: Participate::class, orphanRemoval: true)]
    private Collection $participates;

    public function __construct()
    {
        $this->participates = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDateStart(): \DateTimeInterface
    {
        return $this->dateStart;
    }

    public function setDateStart(\DateTimeInterface $dateStart): self
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    public function getDateClose(): \DateTimeInterface
    {
        return $this->dateClose;
    }

    public function setDateClose(\DateTimeInterface $dateClose): self
    {
        $this->dateClose = $dateClose;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getDateArchived(): ?\DateTimeInterface
    {
        return $this->dateArchived;
    }

    public function setDateArchived(\DateTimeInterface $dateArchived): self
    {
        $this->dateArchived = $dateArchived;

        return $this;
    }

    public function getArchiver(): ?User
    {
        return $this->archiver;
    }

    public function setArchiver(?User $archiver): self
    {
        $this->archiver = $archiver;

        return $this;
    }

    /**
     * @return Collection<int, Participate>
     */
    public function getParticipates(): Collection
    {
        return $this->participates;
    }

    public function addParticipate(Participate $participate): self
    {
        if (!$this->participates->contains($participate)) {
            $this->participates->add($participate);
            $participate->setSanta($this);
        }

        return $this;
    }

    public function removeParticipate(Participate $participate): self
    {
        if ($this->participates->removeElement($participate)) {
            // set the owning side to null (unless already changed)
            if ($participate->getSanta() === $this) {
                $participate->setSanta(null);
            }
        }

        return $this;
    }
}
