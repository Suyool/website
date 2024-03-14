<?php

namespace App\Repository;

use App\Entity\Simly\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<Order>
 *
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SimlyOrdersRepository extends EntityRepository
{

    public function refundedEsims()
    {
        $qb = $this->createQueryBuilder('e')
        ->select('t.transId,t.status,e.status as statusEsim, e.esimId,t.suyoolUserId,t')
        ->leftJoin(Order::class,'t','WITH','t.esims_id = e.id')
        ->where("e.status = 'REFUNDED' and t.status != 'canceled'")
        ->getQuery()
        ->getResult();

        return $qb;
    }

}
