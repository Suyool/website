<?php

namespace App\Entity;

use App\Entity\Traits\EditorTrait;
use App\Entity\Traits\DateTrait;
use App\Entity\Traits\StatusTrait;
use Doctrine\ORM\Mapping as ORM;
use Scheb\TwoFactorBundle\Model\Email\TwoFactorInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

class Admin extends Managers
{

}