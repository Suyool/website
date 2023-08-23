<?php

namespace App\Entity\Shopify;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="suyool_shopify.sessions")
 * @ORM\HasLifecycleCallbacks
 */
class Session
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="bigint")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, name="session_id")
     */
    private $sessionId;

    /**
     * @ORM\Column(type="string", length=255, name="shop")
     */
    private $shop;

    /**
     * @ORM\Column(type="boolean", name="is_online")
     */
    private $isOnline;

    /**
     * @ORM\Column(type="string", length=255, name="state")
     */
    private $state;

    /**
     * @ORM\Column(type="datetime", nullable=true, name="created_at")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true, name="updated_at")
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, name="scope")
     */
    private $scope;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, name="access_token")
     */
    private $accessToken;

    /**
     * @ORM\Column(type="datetime", nullable=true, name="expires_at")
     */
    private $expiresAt;

    /**
     * @ORM\Column(type="bigint", nullable=true, name="user_id")
     */
    private $userId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, name="user_first_name")
     */
    private $userFirstName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, name="user_last_name")
     */
    private $userLastName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, name="user_email")
     */
    private $userEmail;

    /**
     * @ORM\Column(type="boolean", nullable=true, name="user_email_verified")
     */
    private $userEmailVerified;

    /**
     * @ORM\Column(type="boolean", nullable=true, name="account_owner")
     */
    private $accountOwner;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, name="locale")
     */
    private $locale;

    /**
     * @ORM\Column(type="boolean", nullable=true, name="collaborator")
     */
    private $collaborator;

    /**
     * @ORM\PrePersist
     */
    public function updatedTimestamps()
    {
        $this->createdAt = new \DateTime('now');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSessionId(): ?string
    {
        return $this->sessionId;
    }

    public function setSessionId(string $sessionId): self
    {
        $this->sessionId = $sessionId;
        return $this;
    }

    public function getCollaborator(): ?bool
    {
        return $this->collaborator;
    }

    public function setCollaborator(?bool $collaborator): self
    {
        $this->collaborator = $collaborator;
        return $this;
    }

    public function getShop(): ?string
    {
        return $this->shop;
    }

    public function setShop(string $shop): self
    {
        $this->shop = $shop;
        return $this;
    }

    public function getIsOnline(): ?bool
    {
        return $this->isOnline;
    }

    public function setIsOnline(bool $isOnline): self
    {
        $this->isOnline = $isOnline;
        return $this;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function setAccessToken(?string $accessToken): self
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    public function getScope(): ?string
    {
        return $this->scope;
    }

    public function setScope(?string $scope): self
    {
        $this->scope = $scope;
        return $this;
    }

    public function getExpiresAt(): ?\DateTimeInterface
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(?\DateTimeInterface $expiresAt): self
    {
        $this->expiresAt = $expiresAt;
        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(?int $userId): self
    {
        $this->userId = $userId;
        return $this;
    }

    public function getAccountOwner(): ?bool
    {
        return $this->accountOwner;
    }

    public function setAccountOwner(?bool $accountOwner): self
    {
        $this->accountOwner = $accountOwner;
        return $this;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(?string $locale): self
    {
        $this->locale = $locale;
        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;
        return $this;
    }

    public function getUserFirstName(): ?string
    {
        return $this->userFirstName;
    }

    public function setUserFirstName(?string $userFirstName): self
    {
        $this->userFirstName = $userFirstName;
        return $this;
    }

    public function getUserLastName(): ?string
    {
        return $this->userLastName;
    }

    public function setUserLastName(?string $userLastName): self
    {
        $this->userLastName = $userLastName;
        return $this;
    }

    public function getUserEmail(): ?string
    {
        return $this->userEmail;
    }

    public function setUserEmail(?string $userEmail): self
    {
        $this->userEmail = $userEmail;
        return $this;
    }

    public function isUserEmailVerified(): ?bool
    {
        return $this->userEmailVerified;
    }

    public function setUserEmailVerified(?bool $userEmailVerified): self
    {
        $this->userEmailVerified = $userEmailVerified;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
    public function getCreateDateFormat()
    {
        if(isset($this->createdAt)){
            return $this->createdAt->format('h:i Y-m-d');
        }
        else
            return Null;
    }
    public function getUpdatedDateFormat()
    {
        if(isset($this->updatedAt)){
            return $this->updatedAt->format('h:i Y-m-d');
        }
        else
            return Null;
    }
}
