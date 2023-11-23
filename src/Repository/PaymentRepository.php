<?php

namespace App\Repository;

use App\Entity\topup\bob_transactions;
use App\Entity\topup\session;
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

        if ($searchQuery != null) {
            if ($searchQuery['status'] != null && $searchQuery['transId'] != null) {
                $where = "o.status='" . $searchQuery['status'] . "' and o.transId= " . $searchQuery['transId'];
            } else if ($searchQuery['status'] != null) {
                $where = "o.status='" . $searchQuery['status'] . "'";
            } else if ($searchQuery['transId'] != null) {
                $where = "o.transId= " . $searchQuery['transId'];
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
}
