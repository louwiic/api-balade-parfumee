<?php

namespace App\Entity;

use App\Repository\NotificationsUsersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotificationsUsersRepository::class)]
class NotificationsUsers
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToMany(targetEntity: user::class)]
    private Collection $user_id;

    #[ORM\ManyToMany(targetEntity: Notification::class)]
    private Collection $notification_id;

    #[ORM\Column(nullable: true)]
    private ?bool $isRead = null;

    public function __construct()
    {
        $this->user_id = new ArrayCollection();
        $this->notification_id = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, user>
     */
    public function getUserId(): Collection
    {
        return $this->user_id;
    }

    public function addUserId(user $userId): static
    {
        if (!$this->user_id->contains($userId)) {
            $this->user_id->add($userId);
        }

        return $this;
    }

    public function removeUserId(user $userId): static
    {
        $this->user_id->removeElement($userId);
        return $this;
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getNotificationId(): Collection
    {
        return $this->notification_id;
    }

    public function addNotificationId(Notification $notificationId): static
    {
        if (!$this->notification_id->contains($notificationId)) {
            $this->notification_id->add($notificationId);
        }

        return $this;
    }

    public function removeNotificationId(Notification $notificationId): static
    {
        $this->notification_id->removeElement($notificationId);

        return $this;
    }

    public function isIsRead(): ?bool
    {
        return $this->isRead;
    }

    public function setIsRead(?bool $isRead): static
    {
        $this->isRead = $isRead;

        return $this;
    }
}
