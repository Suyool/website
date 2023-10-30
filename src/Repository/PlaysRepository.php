<?php

namespace App\Repository;

use App\Entity\Loto\LOTO_draw;
use App\Entity\Loto\LOTO_results;
use App\Entity\Loto\order;
use App\Entity\Plays;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use PDO;

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
            ->where('l.drawNumber = :drawid and r.drawId = :drawid and l.order = o.id and o.status = :completed')
            ->setParameter('drawid', $drawid)
            ->setParameter('completed', 'completed')
            ->groupBy('o.suyoolUserId')
            ->getQuery()
            ->getResult();
    }

    public function findPlayedUserAndDontPlayThisWeek($lastdraw)
    {
        $day = date('w');
        $monday = $day - 1;
        $current_time = strtotime('now');

        if ($current_time < strtotime('today 10:00:00')) {
            $monday = 6 - $monday;
        }
        $week_start = date('Y-m-d ', strtotime('-' . $monday . ' days'));
        $week_end = date('Y-m-d ', strtotime('+' . (6 - $monday) . ' days'));
        $sixmonth = date("Y-m-d", strtotime("+6 months"));

        $userid = $this->createQueryBuilder('l')
            ->select('o.suyoolUserId')
            ->innerJoin(order::class, 'o')
            ->where("l.created < :week_end and o.created < :week_end and o.status = 'completed' and o.id = l.order and o.suyoolUserId NOT IN (SELECT o2.suyoolUserId from App\Entity\Loto\loto l2 , App\Entity\Loto\order o2 where l2.ticketId != 0 and l2.drawNumber = :lastdraw and o2.id = l2.order)")
            ->setParameter('week_end', $week_end)
            ->setParameter('lastdraw', $lastdraw)
            ->groupBy('o.suyoolUserId')
            ->getQuery()
            ->getResult();
        $response = [];
        $responseArray = [];

        foreach ($userid as $userid) {
            $responseArray = [];
            $result = $this->createQueryBuilder('l')
                ->select('o.suyoolUserId, o.id')
                ->innerJoin(order::class, 'o')
                ->where("o.suyoolUserId = :userid and (l.created < :week_start and o.created < :week_start ) or (l.drawNumber != :lastdraw)  and o.id=l.order and o.suyoolUserId NOT IN (SELECT o2.suyoolUserId from App\Entity\Loto\loto l2 , App\Entity\Loto\order o2 where l2.ticketId != 0 and l2.drawNumber = :lastdraw and o2.id = l2.order) and o.suyoolUserId IN (SELECT o3.suyoolUserId from App\Entity\Loto\order o3 where  o3.status = 'completed')   and o.created < :sixmonth ")
                ->setParameter('week_start', $week_start)
                ->setParameter('userid', $userid)
                ->setParameter('sixmonth', $sixmonth)
                ->setParameter('lastdraw', $lastdraw)
                ->groupBy('o.suyoolUserId')
                ->getQuery()
                ->getResult();

            if (!empty($result)) {
                $responseArray[] = array_merge($response, $result);
            }
        }
        return $responseArray;
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

    public function getResultsPerUser($session, $drawNumber)
    {
        $rawResults = $this->createQueryBuilder('l')
            ->select('l.gridSelected, r.drawdate, r.drawId,l.zeednumbers')
            ->innerJoin(order::class, 'o')
            ->innerJoin(LOTO_results::class, 'r')
            ->where('o.suyoolUserId = :session and l.order = o.id and l.drawNumber = :drawNumber and l.drawNumber = r.drawId and l.ticketId is not null and l.ticketId != 0')
            ->setParameter('session', $session)
            ->setParameter('drawNumber', $drawNumber)
            ->groupBy('l.zeednumbers,l.gridSelected')
            ->getQuery()
            ->getResult();

        $groupedResults = [];
        foreach ($rawResults as $result) {
            $drawdate = $result['drawdate']->format('Y-m-d');
            if (!isset($groupedResults[$drawdate])) {
                $groupedResults[$drawdate] = [
                    'date' => $drawdate,
                    'drawId' => $result['drawId'],
                    'gridSelected' => []
                ];
            }
            $gridSelectedArrays = explode("|", $result['gridSelected']);
            foreach ($gridSelectedArrays as $gridSelectedArray) {
                $numbers = explode(" ", $gridSelectedArray);
                $gridSelectedString = implode(" ", $numbers);
                $groupedResults[$drawdate]['gridSelected'][] = ['gridSelected' => $gridSelectedString, 'zeedSelected' => $result['zeednumbers']];
            }
        }

        return array_values($groupedResults); // Return the grouped results as indexed array
    }

    public function getfetchhistory($session, $drawNumber)
    {
        $rawResults = $this->createQueryBuilder('l')
            ->select('l.gridSelected, d.drawdate, d.drawId,l.zeednumbers')
            ->innerJoin(order::class, 'o')
            ->innerJoin(LOTO_draw::class, 'd')
            ->where('o.suyoolUserId = :session and l.order = o.id and l.drawNumber = :drawNumber and l.drawNumber = d.drawId and l.ticketId is not null and l.ticketId != 0')
            ->setParameter('session', $session)
            ->setParameter('drawNumber', $drawNumber)
            ->groupBy('l.zeednumbers,l.gridSelected')
            ->getQuery()
            ->getResult();

        // dd($rawResults);

        $groupedResults = [];
        foreach ($rawResults as $result) {
            $drawdate = $result['drawdate']->format('Y-m-d');
            if (!isset($groupedResults[$drawdate])) {
                $groupedResults[$drawdate] = [
                    'date' => $drawdate,
                    'drawId' => $result['drawId'],
                    'gridSelected' => []
                ];
            }
            $gridSelectedArrays = explode("|", $result['gridSelected']);
            foreach ($gridSelectedArrays as $gridSelectedArray) {
                $numbers = explode(" ", $gridSelectedArray);
                $gridSelectedString = implode(" ", $numbers);
                $groupedResults[$drawdate]['gridSelected'][] = ['gridSelected' => $gridSelectedString, 'zeedSelected' => $result['zeednumbers']];
            }
        }
        return array_values($groupedResults); // Return the grouped results as indexed array
    }

    public function lotoToBePlayed($heldorder)
    {
        return $this->createQueryBuilder('l')
            ->where('l.order = :heldorder and (l.ticketId = 0 or l.ticketId is null)')
            ->setParameter('heldorder', $heldorder)
            ->getQuery()
            ->getResult();
    }

    public function getGridWithTicket($drawId)
    {
        return $this->createQueryBuilder('l')
            ->select('l')
            ->where('l.drawNumber = :drawId and l.ticketId != 0 and l.ticketId is not null')
            ->setParameter('drawId', $drawId)
            ->getQuery()
            ->getResult();
    }


    public function findOrdersIds($userid, $drawId)
    {
        return $this->createQueryBuilder('l')
            ->select('l')
            ->innerJoin(order::class, 'o')
            ->where('o.suyoolUserId = :userid and o.id = l.order and l.drawNumber = :drawId and (l.ticketId != 0 and l.ticketId is not null)')
            ->setParameter('userid', $userid)
            ->setParameter('drawId', $drawId)
            ->getQuery()
            ->getResult();
    }

    public function getUsersIdWhoPlayesLotoInThisDraw($drawId)
    {
        return $this->createQueryBuilder('l')
            ->select('o.suyoolUserId')
            ->innerJoin(order::class, 'o')
            ->where('o.id = l.order and l.drawNumber = :drawId and (l.ticketId != 0 and l.ticketId is not null)')
            ->groupBy('o.suyoolUserId')
            ->setParameter('drawId', $drawId)
            ->getQuery()
            ->getResult();
    }

    public function findLotoTicketByOrderId($order_id)
    {
        return $this->createQueryBuilder('l')
            ->select('l')
            ->where('l.order = :order_id')
            ->addOrderBy('l.drawNumber', 'ASC')
            ->addOrderBy('l.ticketId', '0')
            ->setParameter('order_id', $order_id)
            ->getQuery()
            ->getResult();
    }

    public function getUsersWhoWon($drawId)
    {
        $qb = $this->createQueryBuilder('l')
            ->select('o.suyoolUserId,COALESCE(SUM(COALESCE(l.winloto, 0) + COALESCE(l.winzeed, 0)), 0) as total,o.id,l.ticketId')
            ->innerJoin(order::class, 'o')
            ->where('o.id = l.order and l.drawNumber = :drawId and (l.ticketId != 0 and l.ticketId is not null) and (l.winloto !=0 and l.winloto is not null or l.winzeed is not null) and l.winningStatus is null')
            ->groupBy('o.id')
            ->setParameter('drawId', $drawId)
            ->getQuery()
            ->getResult();

        $listWinners = [];

        foreach ($qb as $qb) {
            $userId = $qb['suyoolUserId'];
            if (!isset($listWinners[$userId])) {
                $listWinners[$userId] = [
                    'UserAccountID' => $userId,
                    'Currency' => "LBP",
                ];
            }
            $listWinners[$userId]['Amount'][] = $qb['total'];
            $listWinners[$userId]['OrderID'][] = $qb['id'];
            $listWinners[$userId]['TicketID'][] = $qb['ticketId'];
        }
        $listWinners = array_values($listWinners);
        return $listWinners;
    }

    public function getWinTickets($order, $drawId = null)
    {
        $where = "";
        if ($drawId != null) {
            $where = "l.drawNumber = {$drawId} and";
        }

        return $this->createQueryBuilder('l')
            ->where("{$where} l.order = :orderid and l.ticketId is not null and (l.winzeed is not null or l.winloto is not null) and l.winningStatus = :pending ")
            ->setParameter('orderid', $order)
            ->setParameter('pending', 'pending')
            ->getQuery()
            ->getResult();
    }

    public function getWinTicketsWinStNull($order, $drawId = null)
    {
        $where = "";
        if ($drawId != null) {
            $where = "l.drawNumber = {$drawId} and";
        }

        return $this->createQueryBuilder('l')
            ->where("{$where} l.order = :orderid and l.ticketId is not null and (l.winzeed is not null or l.winloto is not null) and l.winningStatus is null")
            ->setParameter('orderid', $order)
            ->getQuery()
            ->getResult();
    }

    public function CheckPurchasedStatus()
    {
        $qb = $this->createQueryBuilder('l')
            ->select('o.id,l.ticketId,l.withZeed,l.bouquet,l.price,o.amount,o.transId,l.drawNumber,o.suyoolUserId,l.gridSelected,d.drawdate,l.zeednumbers')
            ->innerJoin(order::class, 'o')
            ->innerJoin(LOTO_draw::class,'d')
            ->where('o.id=l.order and o.status = :purchased and l.drawNumber = d.drawId')
            ->setParameter('purchased', 'purchased')
            ->getQuery()
            ->getResult();
        $listWinners = [];
        $purchasedsum = 0;
        foreach ($qb as $qb) {
            $userId = $qb['id'];
            if (!isset($listWinners[$userId])) {
                $purchasedsum = 0;
                $additinalData = [];
                $listWinners[$userId] = [];
                // continue;
            }
            if (isset($listWinners[$userId])) {
                $qb['withZeed'] ? $qb['withZeed'] = true : $qb['withZeed'] = false;
                $qb['bouquet'] ? $qb['bouquet'] = true : $qb['bouquet'] = false;
                $additinalData[] = [
                    'ticketId' => $qb['ticketId'],
                    'withZeed' => $qb['withZeed'],
                    'zeed'=>$qb['zeednumbers'],
                    'bouquet' => $qb['bouquet'],
                    'grids'=>$qb['gridSelected']
                ];
                $qb['ticketId'] == 0 ? $purchasedsum = $purchasedsum : $purchasedsum += $qb['price']; 
                // $purchasedsum += $qb['price'];

                $listWinners[$userId] = [
                    'orderId'=>$userId,
                    'TotalPrice' => $purchasedsum,
                    'drawNumber'=>$qb['drawNumber'],
                    'userId'=>$qb['suyoolUserId'],
                    'result'=>$qb['drawdate']->format('d/m/Y'),
                    'Currency' => "LBP",
                    'OrderAmount' => $qb['amount'],
                    'transId' => $qb['transId'],
                    'additionalData' => $additinalData
                ];
            }
        }
        $listWinners = array_values($listWinners);
        return $listWinners;
    }

    public function CompletedTicketsCount(){
        return $this->createQueryBuilder('l')
        ->select('count(l)')
        ->where('l.ticketId != 0 and l.ticketId is not null')
        ->getQuery()
        ->getSingleScalarResult();

    }

    public function CompletedTicketsCountThisMonth(){
        $current_time=date("Y-m-d",strtotime("+1 day"));
        $onemonth = date("Y-m-d", strtotime("-1 months"));
        return $this->createQueryBuilder('l')
        ->select('count(l)')
        ->where('l.ticketId != 0 and l.ticketId is not null and l.created < :current_time and l.created > :onemonth')
        ->setParameter('current_time',$current_time)
        ->setParameter('onemonth',$onemonth)
        ->getQuery()
        ->getSingleScalarResult();
    }

    public function CompletedTicketsSumAmount(){
        return $this->createQueryBuilder('l')
        ->select('sum(l.price)')
        ->where('l.ticketId != 0 and l.ticketId is not null')
        ->getQuery()
        ->getSingleScalarResult();
    }

    
}
