<?php

namespace App\Entity\topup;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="api_keys")
 */
class ApiKey
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="API_key", type="string")
     */
    private $apiKey;

    /**
     * @ORM\Column(name="merchant_id")
     */
    private $merchantId;


    /**
     * @ORM\Column(type="boolean")
     */
    private $env;


    /**
     * @ORM\Column(type="string", name="whitelisted_ips")
     */
    private $whitelistedIps;

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
    public function getMerchantId()
    {
        return $this->merchantId;
    }

    /**
     * @param mixed $merchantId
     */
    public function setMerchantId($merchantId)
    {
        $this->merchantId = $merchantId;
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
}