<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class SubscriptionRepository extends EntityRepository
{
    public function getGridsToPlay(){
        return $this->createQueryBuilder('s')
        ->where('s.canceled = 0 and s.remaining > 0')
        ->getQuery()
        ->getResult();
    }
}
