<?php

namespace App\Repository;

use App\Entity\Loto\LOTO_results;
use App\Entity\Loto\order;
use App\Entity\Plays;
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
class PlaysRepository extends EntityRepository
{
    public function getData($transId, $order)
    {
        return $this->createQueryBuilder('l')
            ->select('l')
            ->innerJoin(order::class, 'o')
            ->where('o.transId = :transId and o.id=l.order and o.id = :order and l.iscompleted = true')
            ->setParameter('transId', $transId)
            ->setParameter('order', $order)
            ->getQuery()
            ->getResult();
    }

    public function getlotonotify($transId, $order)
    {
        return $this->createQueryBuilder('l')
            ->select('l')
            ->innerJoin(order::class, 'o')
            ->where('o.transId = :transId and o.id=l.order and o.status= :completed and o.id = :order and l.iscompleted = true')
            ->setParameter('transId', $transId)
            ->setParameter('order', $order)
            ->setParameter('completed', "completed")
            ->getQuery()
            ->getResult();
    }

    public function findPlayedUser($drawid)
    {
        return $this->createQueryBuilder('l')
            ->select('l.drawNumber,o.id,o.suyoolUserId,r.numbers')
            ->innerJoin(LOTO_results::class, 'r')
            ->innerJoin(order::class, 'o')
            ->where('l.drawNumber = :drawid and r.drawId = :drawid')
            ->setParameter('drawid', $drawid)
            ->groupBy('o.id')
            ->getQuery()
            ->getResult();
    }

    public function findPlayedUserAndDontPlayThisWeek()
    {
        $dateoneweek=date("Y-m-d H:i:s", strtotime("+1 week"));
        $day = date('w');
        $monday=$day - 1;
        $week_start = date('Y-m-d H:i:s', strtotime('-' . $monday. ' days'));
        $week_end = date('Y-m-d H:i:s', strtotime('+' . (6 - $monday) . ' days'));
        // dd($week_end);

        $userid = $this->createQueryBuilder('l')
            ->select('o.suyoolUserId')
            ->innerJoin(order::class, 'o')
            ->where("l.createDate < :week_end and o.createDate < :week_end ")
            ->setParameter('week_end', $week_end)
            ->groupBy('o.suyoolUserId, o.id')
            ->getQuery()
            ->getResult();
            // dd($userid);

        $dateoneweek = date("Y-m-d H:i:s", strtotime("+1 week"));
        // dd($dateoneweek);
        foreach($userid as $userid){
            $response[]= $this->createQueryBuilder('l')
            ->select('o.suyoolUserId, o.id')
            ->innerJoin(order::class, 'o')
            ->where("l.createDate < :week_start and o.createDate < :week_start and o.suyoolUserId = :userid ")
            ->setParameter('week_start', $week_start)
            ->setParameter('userid', $userid)
            ->groupBy('o.suyoolUserId, o.id')
            ->getQuery()
            ->getResult();
        }
        return $response;
    }
}
