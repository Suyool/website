<?php
namespace App\Repository;
use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
class lotonumbersRepository extends EntityRepository
{
    public function findPriceByNumbers($limit)
    {
        return $this->createQueryBuilder('ln')
        ->select('ln')
        ->where('ln.numbers < :limit')
        ->setParameter('limit',$limit)
        ->getQuery()
        ->getResult();
    }
}