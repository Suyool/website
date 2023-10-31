<?php


namespace App\Entity\TerraNet;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="accounts")
 */
class Account
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $customerid;

    /**
     * @ORM\Column(type="string")
     */
    private $PPPLoginName;

    /**
     * @ORM\Column(type="string")
     */
    private $firstname;

    /**
     * @ORM\Column(type="string")
     */
    private $lastname;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCustomerid(): ?string
    {
        return $this->customerid;
    }

    public function setCustomerid(string $customerid): self
    {
        $this->customerid = $customerid;

        return $this;
    }

    public function getPPPLoginName(): ?string
    {
        return $this->PPPLoginName;
    }

    public function setPPPLoginName(string $PPPLoginName): self
    {
        $this->PPPLoginName = $PPPLoginName;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }
}
