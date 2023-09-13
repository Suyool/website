<?php

namespace App\Repository;

use App\Entity\Loto\loto;
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
        $qb = $this->createQueryBuilder('o')
            ->select('l.ticketId,o.id,o.suyoolUserId,o.status,o.subscription,o.amount,o.currency,o.created,o.transId')
            ->innerJoin(loto::class, 'l')
            ->where("o.id = l.order {$where}")
            ->orderBy('o.created', 'DESC')
            ->getQuery()
            ->getResult();

        $array = array();

        foreach ($qb as $row) {
            if (!isset($array[$row['id']])) {
                $array[$row['id']] = ['id' => $row['id'], 'suyoolUserId' => $row['suyoolUserId'], 'status' => $row['status'], 'subscription' => $row['subscription'], 'amount' => $row['amount'], 'currency' => $row['currency'], 'transId' => $row['transId'], 'created' => $row['created']];
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
}
