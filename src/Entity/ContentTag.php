<?php

namespace App\Entity;

use App\Repository\ContentTagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContentTagRepository::class)]
class ContentTag
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'tag', targetEntity: ContentExclusive::class)]
    private Collection $contentExclusives;

    public function __construct()
    {
        $this->contentExclusives = new ArrayCollection();
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

    /**
     * @return Collection<int, ContentExclusive>
     */
    public function getContentExclusives(): Collection
    {
        return $this->contentExclusives;
    }

    public function addContentExclusife(ContentExclusive $contentExclusife): static
    {
        if (!$this->contentExclusives->contains($contentExclusife)) {
            $this->contentExclusives->add($contentExclusife);
            $contentExclusife->setTag($this);
        }

        return $this;
    }

    public function removeContentExclusife(ContentExclusive $contentExclusife): static
    {
        if ($this->contentExclusives->removeElement($contentExclusife)) {
            // set the owning side to null (unless already changed)
            if ($contentExclusife->getTag() === $this) {
                $contentExclusife->setTag(null);
            }
        }

        return $this;
    }
}
