<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class OrdersRepository extends EntityRepository
{
    public function getPlayerInfo($orderId)
    {
        return $this->createQueryBuilder('o')
        ->select('o.suyoolUserId')
        ->where('o.id = :orderId')
        ->setParameter('orderId',$orderId)
        ->getQuery()
        ->getOneOrNullResult();
    }

}
