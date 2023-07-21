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
        $dateoneweek = date("Y-m-d H:i:s", strtotime("+1 week"));
        $day = date('w');
        $monday = $day - 1;
        $current_time = strtotime('now');

        if ($current_time < strtotime('today 10:00:00')) {
            $monday -= 7;
        }
        // dd($monday);
        $week_start = date('Y-m-d ', strtotime('-' . $monday . ' days'));
        $week_end = date('Y-m-d ', strtotime('+' . (6 - $monday) . ' days'));
        // dd($week_start);

        $userid = $this->createQueryBuilder('l')
            ->select('o.suyoolUserId')
            ->innerJoin(order::class, 'o')
            ->where("l.createDate < :week_end and o.createDate < :week_end and o.status = 'completed' ")
            ->setParameter('week_end', $week_end)
            ->groupBy('o.suyoolUserId, o.id')
            ->getQuery()
            ->getResult();
        // dd($userid);

        $dateoneweek = date("Y-m-d H:i:s", strtotime("+1 week"));
        // dd($dateoneweek);
        $response = [];

        foreach ($userid as $userid) {
            $result = $this->createQueryBuilder('l')
                ->select('o.suyoolUserId, o.id')
                ->innerJoin(order::class, 'o')
                ->where("l.createDate < :week_start and o.createDate < :week_start and o.suyoolUserId = :userid ")
                ->setParameter('week_start', $week_start)
                ->setParameter('userid', $userid)
                ->groupBy('o.suyoolUserId, o.id')
                ->getQuery()
                ->getResult();
            if (!empty($result)) {
                $response = array_merge($response, $result);;
            }
        }
        return $response;
    }

    public function completed($orderid)
    {
        return $this->createQueryBuilder('l')
            ->select('l')
            ->where('l.order=:order and l.ticketId != 0 and l.ticketId is not null')
            ->setParameter('order', $orderid)
            ->getQuery()
            ->getResult();
    }

    public function getResultsPerUser($session,$drawNumber)
    {
        $rawResults = $this->createQueryBuilder('l')
            ->select('l.gridSelected, r.drawdate, r.drawId,l.zeednumbers')
            ->innerJoin(order::class, 'o')
            ->innerJoin(LOTO_results::class, 'r')
            ->where('o.suyoolUserId = :session and l.order = o.id and l.drawNumber = :drawNumber and l.drawNumber = r.drawId and l.ticketId is not null')
            ->setParameter('session', $session)
            ->setParameter('drawNumber',$drawNumber)
            ->groupBy('l.gridSelected')
            ->getQuery()
            ->getResult();

        $groupedResults = [];
        foreach ($rawResults as $result) {
            $drawdate = $result['drawdate']->format('Y-m-d');
            if (!isset($groupedResults[$drawdate])) {
                $groupedResults[$drawdate] = [
                    'date' => $drawdate,
                    'drawId' => $result['drawId'],
                    'gridSelected'=>[]
                                ];
            }
            $gridSelectedArrays = explode("|", $result['gridSelected']);
            foreach ($gridSelectedArrays as $gridSelectedArray) {
                $numbers = explode(" ", $gridSelectedArray);
                $gridSelectedString = implode(" ", $numbers);
                $groupedResults[$drawdate]['gridSelected'][] = ['gridSelected'=>$gridSelectedString,'zeedSelected'=>$result['zeednumbers']];
            }
            // $groupedResults[$drawdate]['zeedSelected'][] = $result['zeednumbers'];
        }

        return array_values($groupedResults); // Return the grouped results as indexed array
    }
}
