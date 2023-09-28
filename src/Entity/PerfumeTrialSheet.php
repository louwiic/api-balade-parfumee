<?php

namespace App\Entity;

use App\Repository\PerfumeTrialSheetRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PerfumeTrialSheetRepository::class)]
class PerfumeTrialSheet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $dominantNotes = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $evolutionOfPerfume = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $more = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $less = null;

    #[ORM\ManyToOne]
    private ?Fragrance $fragrance = null;

    #[ORM\ManyToOne(inversedBy: 'perfumeTrialSheets')]
    private ?User $user = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $deleteAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createAt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $impression = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDominantNotes(): ?string
    {
        return $this->dominantNotes;
    }

    public function setDominantNotes(?string $dominantNotes): static
    {
        $this->dominantNotes = $dominantNotes;

        return $this;
    }

    public function getEvolutionOfPerfume(): ?string
    {
        return $this->evolutionOfPerfume;
    }

    public function setEvolutionOfPerfume(?string $evolutionOfPerfume): static
    {
        $this->evolutionOfPerfume = $evolutionOfPerfume;

        return $this;
    }

    public function getMore(): ?string
    {
        return $this->more;
    }

    public function setMore(?string $more): static
    {
        $this->more = $more;

        return $this;
    }

    public function getLess(): ?string
    {
        return $this->less;
    }

    public function setLess(?string $less): static
    {
        $this->less = $less;

        return $this;
    }

    public function getFragrance(): ?Fragrance
    {
        return $this->fragrance;
    }

    public function setFragrance(?Fragrance $fragrance): static
    {
        $this->fragrance = $fragrance;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getDeleteAt(): ?\DateTimeImmutable
    {
        return $this->deleteAt;
    }

    public function setGetDeleteAt(?\DateTimeImmutable $deleteAt): static
    {
        $this->deleteAt = $deleteAt;

        return $this;
    }

    public function getCreateAt(): ?\DateTimeImmutable
    {
        return $this->createAt;
    }

    public function setCreateAt(\DateTimeImmutable $createAt): static
    {
        $this->createAt = $createAt;

        return $this;
    }

    public function getImpression(): ?string
    {
        return $this->impression;
    }

    public function setImpression(?string $impression): static
    {
        $this->impression = $impression;

        return $this;
    }
}
