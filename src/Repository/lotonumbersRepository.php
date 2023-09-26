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
}
