<?php

namespace App\Entity;

use App\Repository\NotificationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
class Notification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("notification:read")]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups("notification:read")]
    private ?string $title = null;

    #[ORM\ManyToOne(inversedBy: 'notifications')]
    #[Groups("notification:read")]
    private ?CategoryNotification $categoryNotification = null;

    #[ORM\Column]
    #[Groups("notification:read")]
    private ?int $totalSend = null;

    #[ORM\Column]
    #[Groups("notification:read")]
    private ?int $notificationOpen = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'notifications')]
    private Collection $user;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups("notification:read")]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups("notification:read")]
    private ?\DateTimeImmutable $createAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups("notification:read")]
    private ?\DateTimeImmutable $deletedAt = null;




    public function __construct()
    {
        $this->user = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getCategoryNotification(): ?CategoryNotification
    {
        return $this->categoryNotification;
    }

    public function setCategoryNotification(?CategoryNotification $categoryNotification): static
    {
        $this->categoryNotification = $categoryNotification;

        return $this;
    }

    public function getTotalSend(): ?int
    {
        return $this->totalSend;
    }

    public function setTotalSend(int $totalSend): static
    {
        $this->totalSend = $totalSend;

        return $this;
    }

    public function getNotificationOpen(): ?int
    {
        return $this->notificationOpen;
    }

    public function setNotificationOpen(int $notificationOpen): static
    {
        $this->notificationOpen = $notificationOpen;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    public function addUser(User $user): static
    {
        if (!$this->user->contains($user)) {
            $this->user->add($user);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        $this->user->removeElement($user);

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

    public function getCreateAt(): ?\DateTimeImmutable
    {
        return $this->createAt;
    }

    public function setCreateAt(\DateTimeImmutable $createAt): static
    {
        $this->createAt = $createAt;

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeImmutable $deletedAt): static
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }
}
