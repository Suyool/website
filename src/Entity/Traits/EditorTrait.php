<?php

namespace App\Entity\Traits;

use App\Entity\Managers;
use Doctrine\ORM\Mapping as ORM;

trait EditorTrait
{
    /**
     * @var integer
     *
     * @ORM\Column(name="creator", type="integer", nullable=true)
     */
    private $creator;

    /**
     * @var integer
     *
     * @ORM\Column(name="updator", type="integer", nullable=true)
     */
    private $updator;

    /**
     * @var string
     *
     * @ORM\Column(name="creator_ip", type="string", length=210, nullable=true)
     */
    private $creatorIp;

    /**
     * @var string
     *
     * @ORM\Column(name="updator_ip", type="string", length=100, nullable=true)
     */
    private $updatorIp;

    /**
     * @var Managers
     *
     * @ORM\ManyToOne(targetEntity="Managers")
     * @ORM\JoinColumn(name="creator", referencedColumnName="id")
     */
    public $creatorData;

    /**
     * @return int
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * @param int $creator
     * @return EditorTrait
     */
    public function setCreator($creator)
    {
        $this->creator = $creator;
        return $this;
    }

    /**
     * @return int
     */
    public function getUpdator()
    {
        return $this->updator;
    }

    /**
     * @param int $updator
     * @return EditorTrait
     */
    public function setUpdator($updator)
    {
        $this->updator = $updator;
        return $this;
    }

    /**
     * @return string
     */
    public function getCreatorIp()
    {
        return $this->creatorIp;
    }

    /**
     * @param string $creatorIp
     * @return EditorTrait
     */
    public function setCreatorIp($creatorIp)
    {
        $this->creatorIp = $creatorIp;
        return $this;
    }

    /**
     * @return string
     */
    public function getUpdatorIp()
    {
        return $this->updatorIp;
    }

    /**
     * @param string $updatorIp
     * @return EditorTrait
     */
    public function setUpdatorIp($updatorIp)
    {
        $this->updatorIp = $updatorIp;
        return $this;
    }

    /**
     * @return Managers
     */
    public function getCreatorData()
    {
        return $this->creatorData;
    }

    /**
     * @param Managers $creatorData
     * @return EditorTrait
     */
    public function setCreatorData($creatorData)
    {
        $this->creatorData = $creatorData;
        return $this;
    }
}