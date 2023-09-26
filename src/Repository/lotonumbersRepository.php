<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class lotonumbersRepository extends EntityRepository
{
    public function findPriceByNumbers($limit)
    {
        return $this->createQueryBuilder('ln')
            ->select('ln')
            ->where('ln.numbers < :limit')
            ->setParameter('limit', $limit)
            ->getQuery()
            ->getResult();
    }

    public function truncate()
    {
        return $this->createQueryBuilder('ln')
            ->delete()
            ->getQuery()
            ->execute();
    }

    public function Increment()
    {
     $conn=$this->getEntityManager()->getConnection();
         $sql="ALTER TABLE prices AUTO_INCREMENT = 1";
         $stmt=$conn->prepare($sql);
         $res=$stmt->execute();
         return $res->fetch();
 
    }
}
