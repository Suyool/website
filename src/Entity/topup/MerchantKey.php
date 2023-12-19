<?php


namespace App\Entity\topup;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="api_keys")
 */
class MerchantKey
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="description", type="string")
     */
    private $description;

    /**
     * @ORM\Column(name="API_key", type="string")
     */
    private $apiKey;

    /**
     * @ORM\ManyToOne(targetEntity="merchants",fetch="EAGER")
     * @ORM\JoinColumn(name="merchant_id", referencedColumnName="id")
     */
    private $merchant;

    /**
     * @ORM\Column(type="boolean")
     */
    private $env;


    /**
     * @ORM\Column(type="string", name="whitelisted_ips")
     */
    private $whitelistedIps;

    /**
     * @ORM\Column (type="string")
     */
    private $status;


    /**
     * @ORM\Column(type="datetime", name="expiry_date")
     */
    private $expiryDate;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @param mixed $apiKey
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMerchant()
    {
        return $this->merchant;
    }

    /**
     * @param mixed
     */
    public function setMerchant($merchantId)
    {
        $this->merchant = $merchantId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getEnv()
    {
        return $this->env;
    }

    /**
     * @param mixed $env
     */
    public function setEnv($env)
    {
        $this->env = $env;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getWhitelistedIps()
    {
        return $this->whitelistedIps;
    }

    /**
     * @param mixed $whitelistedIps
     */
    public function setWhitelistedIps($whitelistedIps)
    {
        $this->whitelistedIps = $whitelistedIps;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getExpiryDate()
    {
        return $this->expiryDate;
    }

    /**
     * @param mixed $expiryDate
     */
    public function setExpiryDate($expiryDate)
    {
        $this->expiryDate = $expiryDate;
        return $this;
    }
}