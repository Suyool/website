<?php

namespace App\Entity\Shopify;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="sessions")
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

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function setAccessToken(?string $accessToken): self
    {
        $this->accessToken = $accessToken;

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
}
