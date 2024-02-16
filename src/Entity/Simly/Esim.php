<?php

namespace App\Entity\Simly;

use Doctrine\ORM\Mapping as ORM;

/**
// * @ORM\Entity(repositoryClass="App\Repository\SimlyOrdersRepository")
 * @ORM\Table(name="esima")
 */

class Esim
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $esimId;

    /**
     * @ORM\Column(type="integer")
     */
    private $suyoolUserId;

    /**
     * @ORM\Column(type="string")
     */
    private $status;

    /**
     * @ORM\Column(type="string")
     */
    private $smdp;

    /**
     * @ORM\Column(type="string")
     */
    private $matchingId;

    /**
     * @ORM\Column(type="string")
     */
    private $qrCodeImageUrl;

    /**
     * @ORM\Column(type="string")
     */
    private $qrCodeString;

    /**
     * @ORM\Column(type="string")
     */
    private $topups;

    /**
     * @ORM\Column(type="string")
     */
    private $transaction;

    /**
     * @ORM\Column(type="string")
     */
    private $plan;

    /**
     * @ORM\Column(type="string")
     */
    private $allowedPlans;

}