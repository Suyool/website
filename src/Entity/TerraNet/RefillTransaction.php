<?php


namespace App\Entity\TerraNet;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="refill_transactions")
 */
class RefillTransaction
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
    private $PPPLoginName;

    /**
     * @ORM\Column(type="integer")
     */
    private $ProductId;

    /**
     * @ORM\Column(type="string")
     */
    private $TransactionID;

    /**
     * @ORM\Column(type="integer")
     */
    private $ErrorCode;

    /**
     * @ORM\Column(type="string")
     */
    private $ErrorMessage;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getProductId(): ?int
    {
        return $this->ProductId;
    }

    public function setProductId(int $ProductId): self
    {
        $this->ProductId = $ProductId;

        return $this;
    }

    public function getTransactionID(): ?string
    {
        return $this->TransactionID;
    }

    public function setTransactionID(string $TransactionID): self
    {
        $this->TransactionID = $TransactionID;

        return $this;
    }

    public function getErrorCode(): ?int
    {
        return $this->ErrorCode;
    }

    public function setErrorCode(int $ErrorCode): self
    {
        $this->ErrorCode = $ErrorCode;

        return $this;
    }

    public function getErrorMessage(): ?string
    {
        return $this->ErrorMessage;
    }

    public function setErrorMessage(string $ErrorMessage): self
    {
        $this->ErrorMessage = $ErrorMessage;

        return $this;
    }
}
