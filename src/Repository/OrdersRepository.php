<?php

namespace App\Repository;

use App\Entity\Loto\loto;
use App\Entity\Loto\order;
use App\Entity\Notification\Users;
use Doctrine\ORM\EntityRepository;

class OrdersRepository extends EntityRepository
{
    public function getPlayerInfo($orderId)
    {
        return $this->createQueryBuilder('o')
            ->select('o.suyoolUserId')
            ->where('o.id = :orderId')
            ->setParameter('orderId', $orderId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function OrderSubscription($searchQuery = null)
    {
        $where = "";

        if ($searchQuery != null) {
            if ($searchQuery['status'] != null && $searchQuery['amount'] != null) {
                $where = "and o.status='" . $searchQuery['status'] . "' and o.amount " . $searchQuery['amount'];
            } else if ($searchQuery['status'] != null) {
                $where = "and o.status='" . $searchQuery['status'] . "'";
            } else if ($searchQuery['amount'] != null) {
                $where = "and o.amount " . $searchQuery['amount'];
            }
        }

        $connection = $this->getEntityManager()->getConnection();
        $sql = "select
        l.ticketId,o.id,o.suyoolUserId,u.fname,u.lname,o.status,o.subscription,o.amount,o.currency,o.created,o.transId,o.errorInfo as error
        FROM suyool_loto.orders o LEFT JOIN suyool_loto.loto l ON o.id = l.order_id LEFT JOIN  suyool_notification.users u ON o.suyoolUserId = u.suyoolUserId
        WHERE o.id = l.order_id {$where}
        ORDER BY o.created DESC
         ";

        $stmt = $connection->prepare($sql);
        $result = $stmt->execute();
        $qb = $result->fetchAll();

        // $qb = $this->getEntityManager()->createQueryBuilder()
        //     ->select('l.ticketId,o.id,o.suyoolUserId,o.status,o.subscription,o.amount,o.currency,o.created,o.transId')
        //     ->from('App\Entity\Loto\order', 'o')
        //     ->leftJoin('App\Entity\Loto\loto','WITH','o.id =l.order')
        //     ->leftJoin('App\Entity\Notification\Users','u','WITH','o.suyoolUserId = u.suyoolUserId')
        //     ->where("o.id = l.order {$where}")
        //     ->orderBy('o.created', 'DESC')
        //     ->getQuery()
        //     ->getResult();

        $array = array();

        foreach ($qb as $row) {
            if (!isset($array[$row['id']])) {
                $array[$row['id']] = ['id' => $row['id'], 'suyoolUserId' => $row['suyoolUserId'], 'fname' => $row['fname'], 'lname' => $row['lname'], 'status' => $row['status'], 'subscription' => $row['subscription'], 'amount' => $row['amount'], 'currency' => $row['currency'], 'transId' => $row['transId'], 'error' => $row['error'], 'created' => $row['created']];
                if ($row['ticketId'] == null || $row['ticketId'] == 0) {
                    $array[$row['id']]['redFlag'] = true;
                }
            } else {
                if ($row['ticketId'] == null || $row['ticketId'] == 0) {
                    $array[$row['id']]['redFlag'] = true;
                }
            }
        }

        return array_merge($array);
    }

    public function CountStatusTickets()
    {
        $queryResult = $this->createQueryBuilder('o')
            ->select("o.status, COUNT(o) AS ticketCount")
            ->groupBy('o.status')
            ->getQuery()
            ->getResult();
        $resultArray = [];
        foreach ($queryResult as $row) {
            $resultArray[$row['status']] = $row['ticketCount'];
        }

        return $resultArray;
    }
}
