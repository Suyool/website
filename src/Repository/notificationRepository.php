<?php

namespace App\Repository;

use App\Entity\Loto\order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Plays>
 *
 * @method Plays|null find($id, $lockMode = null, $lockVersion = null)
 * @method Plays|null findOneBy(array $criteria, array $orderBy = null)
 * @method Plays[]    findAll()
 * @method Plays[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class notificationRepository extends EntityRepository
{
    public function findPlayedUser($resultDate)
    {
        return $this->createQueryBuilder('n')
            ->select('n')
            ->innerJoin(order::class,'o')
            ->where('n.resultDate = resultDate')
            ->setParameter('resultDate',$resultDate)
            ->groupBy('n.resultDate')
            ->getQuery()
            ->getResult();
    }

    public function getlotonotify($transId,$order)
    {
        return $this->createQueryBuilder('l')
            ->select('l')
            ->innerJoin(order::class,'o')
            ->where('o.transId = :transId and o.id=l.order and o.status= :completed and o.id = :order and l.iscompleted = true')
            ->setParameter('transId',$transId)
            ->setParameter('order',$order)
            ->setParameter('completed',"completed")
            ->getQuery()
            ->getResult();
    }
}
