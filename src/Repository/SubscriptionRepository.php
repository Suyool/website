<?php

namespace App\Repository;

use App\Entity\Loto\loto;
use Doctrine\ORM\EntityRepository;

class SubscriptionRepository extends EntityRepository
{

    public function getGridsToPlay(){
        return $this->createQueryBuilder('s')
        ->where('s.canceled = 0 and s.remaining > 0')
        ->getQuery()
        ->getResult();
    }

    public function getGridsToPlayPerUser($drawnumber,$user){
        return $this->createQueryBuilder('s')
        ->innerJoin(loto::class,'l')
        ->where("s.suyoolUserId = {$user} and s.canceled = 0 and s.remaining > 0 and s.gridSelected NOT IN (SELECT lot.gridSelected FROM App\Entity\Loto\loto lot WHERE lot.drawNumber = {$drawnumber} and lot.ticketId != 0)")
        ->groupBy('s.gridSelected')
        ->getQuery()
        ->getResult();
    }
}
