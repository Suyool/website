<?php

namespace App\Repository;

use App\Entity\topup\attempts;
use App\Entity\topup\bob_transactions;
use App\Entity\topup\session;
use DateTime;
use Doctrine\ORM\EntityRepository;

class PaymentRepository extends EntityRepository
{
    public function CountStatus()
    {
        $queryResult = $this->createQueryBuilder('o')
            ->select("o.status, COUNT(o) AS statusCount")
            ->groupBy('o.status')
            ->getQuery()
            ->getResult();
        $resultArray = [];
        foreach ($queryResult as $row) {
            $resultArray[$row['status']] = $row['statusCount'];
        }

        return $resultArray;
    }

    public function fetchAllPaymentDetails($searchQuery = null)
    {

        $where = "";
        // dd($searchQuery);
        if ($searchQuery != null) {
            if ($searchQuery['status'] != null && $searchQuery['transId'] != null && $searchQuery['currency'] != null && $searchQuery['created'] != null) {
                $where = "o.status='" . $searchQuery['status'] . "' and o.transId= " . $searchQuery['transId'] . " and o.currency ='" . $searchQuery['currency'] . "' and o.created >= '" . $searchQuery['created'] ."'";
            }else if($searchQuery['status'] != null && $searchQuery['currency'] != null && $searchQuery['created'] != null){
                $where = "o.status='" . $searchQuery['status'] . "' and o.currency= '" . $searchQuery['currency'] . "' and  o.created >= '" . $searchQuery['created'] ."'";
            }  
            else if($searchQuery['currency'] != null && $searchQuery['created'] != null){
                $where = "o.currency= '" . $searchQuery['currency'] . "' and  o.created >= '" . $searchQuery['created'] ."'";
            } 
            else if ($searchQuery['status'] != null) {
                $where = "o.status='" . $searchQuery['status'] . "'";
            } else if ($searchQuery['transId'] != null) {
                $where = "o.transId= " . $searchQuery['transId'];
            } else if ($searchQuery['currency'] != null) {
                $where = "o.currency= '" . $searchQuery['currency'] . "'";
            }      
            else if ($searchQuery['created'] != null) {
                $where = "o.created >= '" . $searchQuery['created'] ."'";
            }
        }

        $queryBuilder = $this->createQueryBuilder('o')
            ->select('o.id,o.suyoolUserId,o.transId,o.status,o.amount,o.currency,o.created,s.session,t.status as transactionStatus,t.flagCode,t.error')
            ->leftJoin(session::class, 's', 'WITH', 'o.id = s.orders')
            ->leftJoin(bob_transactions::class, 't', 'WITH', 's.id=t.session');

        if (!empty($where)) {
            $queryBuilder->where($where);
        }

        return $queryBuilder
            ->orderBy('o.created', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findPendingTransactionsAfter10Minutes()
    {
        $tenMinutesAgo = new DateTime();
        $tenMinutesAgo->modify('-10 minutes');
        $tenMinutesAgo = $tenMinutesAgo->format('Y-m-d H:i:s');

        return $this->createQueryBuilder('o')
            ->select('s')
            ->leftJoin(session::class, 's', 'WITH', 's.orders = o.id')
            ->where("o.status = 'pending' and o.created > '2023-12-11 00:00:00' and o.created < :tenMinutesAgo")
            ->setParameter('tenMinutesAgo', $tenMinutesAgo)
            ->getQuery()
            ->getResult();
    }

    public function findPendingTransactionsAfter10MinutesOrder()
    {
        $tenMinutesAgo = new DateTime();
        $tenMinutesAgo->modify('-10 minutes');
        $tenMinutesAgo = $tenMinutesAgo->format('Y-m-d H:i:s');

        return $this->createQueryBuilder('o')
            ->select('o')
            ->leftJoin(session::class, 's', 'WITH', 's.orders = o.id')
            ->where("o.status = 'pending' and o.created > '2023-12-11 00:00:00' and o.created < :tenMinutesAgo")
            ->andWhere('s.id IS NULL')
            ->setParameter('tenMinutesAgo', $tenMinutesAgo)
            ->getQuery()
            ->getResult();
    }

    public function findTransactionsThatIsNotCompleted($transId)
    {
        return $this->createQueryBuilder('o')
            ->select('o,s.session')
            ->leftJoin(session::class,'s','WITH','o.id = s.orders')
            ->where("o.status != 'completed' and o.transId = {$transId}")
            ->orderBy('o.created', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getTransactions($currency,$from)
    {
        $where = "o.status = 'completed' ";
        if(!empty($currency) && !empty($from)){
            $where .= " and o.currency = '{$currency}' and o.created >= '{$from}'";
        }else if(!empty($currency)){
            $where .= " and o.currency = '{$currency}'";
        }else if(!empty($from)){
            $where .= " and o.created >= '{$from}'";
        }
        return $this->createQueryBuilder('o')
        ->select('o.suyoolUserId,o.transId,o.amount,o.currency,o.status,a.status as statuscard,a.card,a.name,o.created')
        ->leftJoin(attempts::class,'a','WITH','o.transId = a.transactionId')
        ->where($where)
        ->groupBy('a.transactionId')
        ->getQuery()
        ->getResult();
    }

    public function getTheSession($orderid)
    {
        return $this->createQueryBuilder('o')
        ->select('s.session,o.suyoolUserId','o.details')
        ->leftJoin(session::class,'s','WITH','o.id = s.orders')
        ->where("o.transId = {$orderid} and o.status != 'completed'")
        ->orderBy('o.created', 'DESC')
        ->groupBy('s.orders')
        ->setMaxResults(1)
        ->getQuery()
        ->getOneOrNullResult();
    }
}
