<?php

namespace App\Entity;

use App\Repository\ProfilRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProfilRepository::class)]
class Profil
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $notes_to_discover = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $childhood_scents = null;

    #[ORM\ManyToOne]
    private ?Fragrance $mySymbolicFragrance = null;

    #[ORM\OneToOne(inversedBy: 'profil', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(nullable: true)]
    private ?bool $feltOnMyCollection = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNotesToDiscover(): ?string
    {
        return $this->notes_to_discover;
    }

    public function setNotesToDiscover(string $notes_to_discover): static
    {
        $this->notes_to_discover = $notes_to_discover;

        return $this;
    }

    public function getChildhoodScents(): ?string
    {
        return $this->childhood_scents;
    }

    public function setChildhoodScents(?string $childhood_scents): static
    {
        $this->childhood_scents = $childhood_scents;

        return $this;
    }

    public function getMySymbolicFragrance(): ?Fragrance
    {
        return $this->mySymbolicFragrance;
    }

    public function setMySymbolicFragrance(?Fragrance $mySymbolicFragrance): static
    {
        $this->mySymbolicFragrance = $mySymbolicFragrance;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function isFeltOnMyCollection(): ?bool
    {
        return $this->feltOnMyCollection;
    }

    public function setFeltOnMyCollection(?bool $feltOnMyCollection): static
    {
        $this->feltOnMyCollection = $feltOnMyCollection;

        return $this;
    }
}
