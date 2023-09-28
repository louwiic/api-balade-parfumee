<?php

namespace App\Entity;

use App\Repository\FragranceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FragranceRepository::class)]
class Fragrance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $brand = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $img = null;

   #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $createAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $concentration = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $value = null;

    #[ORM\OneToMany(mappedBy: 'fragrance', targetEntity: CheckList::class)]
    private Collection $checkLists;

    #[ORM\ManyToMany(targetEntity: ReviewPerfumeNote::class, mappedBy: 'fragrance')]
    private Collection $reviewPerfumeNotes;

    public function __construct()
    {
        $this->checkLists = new ArrayCollection();
        $this->reviewPerfumeNotes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): static
    {
        $this->brand = $brand;

        return $this;
    }

    public function getImg(): ?string
    {
        return $this->img;
    }

    public function setImg(string $img): static
    {
        $this->img = $img;

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

    public function getConcentration(): ?string
    {
        return $this->concentration;
    }

    public function setConcentration(?string $concentration): static
    {
        $this->concentration = $concentration;

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

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function addCheckList(CheckList $checkList): static
    {
        if (!$this->checkLists->contains($checkList)) {
            $this->checkLists->add($checkList);
            $checkList->setFragrance($this);
        }

        return $this;
    }

    public function removeCheckList(CheckList $checkList): static
    {
        if ($this->checkLists->removeElement($checkList)) {
            // set the owning side to null (unless already changed)
            if ($checkList->getFragrance() === $this) {
                $checkList->setFragrance(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ReviewPerfumeNote>
     */
    public function getReviewPerfumeNotes(): Collection
    {
        return $this->reviewPerfumeNotes;
    }

    public function addReviewPerfumeNote(ReviewPerfumeNote $reviewPerfumeNote): static
    {
        if (!$this->reviewPerfumeNotes->contains($reviewPerfumeNote)) {
            $this->reviewPerfumeNotes->add($reviewPerfumeNote);
            $reviewPerfumeNote->addFragrance($this);
        }

        return $this;
    }

    public function removeReviewPerfumeNote(ReviewPerfumeNote $reviewPerfumeNote): static
    {
        if ($this->reviewPerfumeNotes->removeElement($reviewPerfumeNote)) {
            $reviewPerfumeNote->removeFragrance($this);
        }

        return $this;
    }
}
