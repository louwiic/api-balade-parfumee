<?php

namespace App\Entity;

use App\Repository\LayeringRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LayeringRepository::class)]
class Layering
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    private ?Fragrance $fragrance1 = null;

    #[ORM\ManyToOne]
    private ?Fragrance $fragrance2 = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $deletedAT = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $createAt = null;

    #[ORM\ManyToOne(inversedBy: 'layerings')]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFragrance1(): ?Fragrance
    {
        return $this->fragrance1;
    }

    public function setFragrance1(?Fragrance $fragrance1): static
    {
        $this->fragrance1 = $fragrance1;

        return $this;
    }

    public function getFragrance2(): ?Fragrance
    {
        return $this->fragrance2;
    }

    public function setFragrance2(?Fragrance $fragrance2): static
    {
        $this->fragrance2 = $fragrance2;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDeletedAT(): ?\DateTimeImmutable
    {
        return $this->deletedAT;
    }

    public function setDeletedAT(?\DateTimeImmutable $deletedAT): static
    {
        $this->deletedAT = $deletedAT;

        return $this;
    }

    public function getCreateAt(): ?\DateTimeImmutable
    {
        return $this->createAt;
    }

    public function setCreateAt(?\DateTimeImmutable $createAt): static
    {
        $this->createAt = $createAt;

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
}
