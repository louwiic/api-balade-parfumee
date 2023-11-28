<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 13, unique: true)]
    private ?string $phone = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?Profil $profil = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: MyFavoriteTypesOfPerfumes::class)]
    private Collection $myFavoriteTypesOfPerfumes;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $idClientStripe = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $subscriptionAt = null;

    #[ORM\Column]
    private ?int $typeSubscription = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Wishlist::class)]
    private Collection $wishlists;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: CheckList::class)]
    private Collection $checkLists;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $idSubscriptionStripe = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: PerfumeTrialSheet::class)]
    private Collection $perfumeTrialSheets;

    #[ORM\ManyToMany(targetEntity: Notification::class, mappedBy: 'user')]
    private Collection $notifications;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: ReviewPerfumeNote::class)]
    private Collection $reviewPerfumeNotes;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Layering::class)]
    private Collection $layerings;

    #[ORM\Column(nullable: true)]
    private ?bool $cguAccepted = null;

    #[ORM\Column(nullable: true)]
    private ?bool $cgvAccepted = null;

    #[ORM\Column(nullable: true)]
    private ?bool $politicsAccepted = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $avatar = null;

    public function __construct()
    {
        $this->myFavoriteTypesOfPerfumes = new ArrayCollection();
        $this->wishlists = new ArrayCollection();
        $this->checkLists = new ArrayCollection();
        $this->perfumeTrialSheets = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->reviewPerfumeNotes = new ArrayCollection();
        $this->layerings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getProfil(): ?Profil
    {
        return $this->profil;
    }

    public function setProfil(Profil $profil): static
    {
        // set the owning side of the relation if necessary
        if ($profil->getUser() !== $this) {
            $profil->setUser($this);
        }

        $this->profil = $profil;

        return $this;
    }

    /**
     * @return Collection<int, MyFavoriteTypesOfPerfumes>
     */
    public function getMyFavoriteTypesOfPerfumes(): Collection
    {
        return $this->myFavoriteTypesOfPerfumes;
    }

    public function addMyFavoriteTypesOfPerfume(MyFavoriteTypesOfPerfumes $myFavoriteTypesOfPerfume): static
    {
        if (!$this->myFavoriteTypesOfPerfumes->contains($myFavoriteTypesOfPerfume)) {
            $this->myFavoriteTypesOfPerfumes->add($myFavoriteTypesOfPerfume);
            $myFavoriteTypesOfPerfume->setUser($this);
        }

        return $this;
    }

    public function removeMyFavoriteTypesOfPerfume(MyFavoriteTypesOfPerfumes $myFavoriteTypesOfPerfume): static
    {
        if ($this->myFavoriteTypesOfPerfumes->removeElement($myFavoriteTypesOfPerfume)) {
            // set the owning side to null (unless already changed)
            if ($myFavoriteTypesOfPerfume->getUser() === $this) {
                $myFavoriteTypesOfPerfume->setUser(null);
            }
        }

        return $this;
    }

    public function getIdClientStripe(): ?string
    {
        return $this->idClientStripe;
    }

    public function setIdClientStripe(?string $idClientStripe): static
    {
        $this->idClientStripe = $idClientStripe;

        return $this;
    }

    public function getSubscriptionAt(): ?\DateTimeInterface
    {
        return $this->subscriptionAt;
    }

    public function setSubscriptionAt(?\DateTimeInterface $subscriptionAt): static
    {
        $this->subscriptionAt = $subscriptionAt;

        return $this;
    }

    public function getTypeSubscription(): ?int
    {
        return $this->typeSubscription;
    }

    public function setTypeSubscription(int $typeSubscription): static
    {
        $this->typeSubscription = $typeSubscription;

        return $this;
    }

    /**
     * @return Collection<int, Wishlist>
     */
    public function getWishlists(): Collection
    {
        return $this->wishlists;
    }

    public function addWishlist(Wishlist $wishlist): static
    {
        if (!$this->wishlists->contains($wishlist)) {
            $this->wishlists->add($wishlist);
            $wishlist->setUser($this);
        }

        return $this;
    }

    public function removeWishlist(Wishlist $wishlist): static
    {
        if ($this->wishlists->removeElement($wishlist)) {
            // set the owning side to null (unless already changed)
            if ($wishlist->getUser() === $this) {
                $wishlist->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CheckList>
     */
    public function getCheckLists(): Collection
    {
        return $this->checkLists;
    }

    public function addCheckList(CheckList $checkList): static
    {
        if (!$this->checkLists->contains($checkList)) {
            $this->checkLists->add($checkList);
            $checkList->setUser($this);
        }

        return $this;
    }

    public function removeCheckList(CheckList $checkList): static
    {
        if ($this->checkLists->removeElement($checkList)) {
            // set the owning side to null (unless already changed)
            if ($checkList->getUser() === $this) {
                $checkList->setUser(null);
            }
        }

        return $this;
    }

    public function getIdSubscriptionStripe(): ?string
    {
        return $this->idSubscriptionStripe;
    }

    public function setIdSubscriptionStripe(?string $idSubscriptionStripe): static
    {
        $this->idSubscriptionStripe = $idSubscriptionStripe;

        return $this;
    }

    /**
     * @return Collection<int, PerfumeTrialSheet>
     */
    public function getPerfumeTrialSheets(): Collection
    {
        return $this->perfumeTrialSheets;
    }

    public function addPerfumeTrialSheet(PerfumeTrialSheet $perfumeTrialSheet): static
    {
        if (!$this->perfumeTrialSheets->contains($perfumeTrialSheet)) {
            $this->perfumeTrialSheets->add($perfumeTrialSheet);
            $perfumeTrialSheet->setUser($this);
        }

        return $this;
    }

    public function removePerfumeTrialSheet(PerfumeTrialSheet $perfumeTrialSheet): static
    {
        if ($this->perfumeTrialSheets->removeElement($perfumeTrialSheet)) {
            // set the owning side to null (unless already changed)
            if ($perfumeTrialSheet->getUser() === $this) {
                $perfumeTrialSheet->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): static
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications->add($notification);
            $notification->addUser($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): static
    {
        if ($this->notifications->removeElement($notification)) {
            $notification->removeUser($this);
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
            $reviewPerfumeNote->setUser($this);
        }

        return $this;
    }

    public function removeReviewPerfumeNote(ReviewPerfumeNote $reviewPerfumeNote): static
    {
        if ($this->reviewPerfumeNotes->removeElement($reviewPerfumeNote)) {
            // set the owning side to null (unless already changed)
            if ($reviewPerfumeNote->getUser() === $this) {
                $reviewPerfumeNote->setUser(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, Layering>
     */
    public function getLayerings(): Collection
    {
        return $this->layerings;
    }

    public function addLayering(Layering $layering): static
    {
        if (!$this->layerings->contains($layering)) {
            $this->layerings->add($layering);
            $layering->setUser($this);
        }

        return $this;
    }

    public function removeLayering(Layering $layering): static
    {
        if ($this->layerings->removeElement($layering)) {
            // set the owning side to null (unless already changed)
            if ($layering->getUser() === $this) {
                $layering->setUser(null);
            }
        }

        return $this;
    }

    public function isCguAccepted(): ?bool
    {
        return $this->cguAccepted;
    }

    public function setCguAccepted(?bool $cguAccepted): static
    {
        $this->cguAccepted = $cguAccepted;

        return $this;
    }

    public function isCgvAccepted(): ?bool
    {
        return $this->cgvAccepted;
    }

    public function setCgvAccepted(?bool $cgvAccepted): static
    {
        $this->cgvAccepted = $cgvAccepted;

        return $this;
    }

    public function isPoliticsAccepted(): ?bool
    {
        return $this->politicsAccepted;
    }

    public function setPoliticsAccepted(?bool $politicsAccepted): static
    {
        $this->politicsAccepted = $politicsAccepted;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): static
    {
        $this->avatar = $avatar;

        return $this;
    }
}
