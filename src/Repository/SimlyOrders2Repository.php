<?php

namespace App\Repository;

use App\Entity\Simly\Esim;
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
class SimlyOrders2Repository extends EntityRepository
{

    public function fetchIfUserHasBoughtThisEsim($suyoolUserId)
    {
        $qb = $this->createQueryBuilder('t')
        ->select('t')
        ->leftJoin(Esim::class,'e','WITH','t.esims_id = e.id')
        ->where("t.status = 'completed' and e.status != 'REFUNDED' and t.suyoolUserId = {$suyoolUserId} and t.isOffre = 1")
        ->getQuery()
        ->getResult();

        return $qb;
    }

}
