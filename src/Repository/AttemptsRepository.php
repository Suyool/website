<?php

namespace App\Repository;

use DateTime;
use Doctrine\ORM\EntityRepository;

class AttemptsRepository extends EntityRepository
{
    public function GetTransactionsPerCard($cardnumber)
    {
        $now = new DateTime();
        $now = $now->format('Y-m-d H:i:s');
        $yesterday = new DateTime();
        $yesterday->modify('-1 day');
        $yesterday = $yesterday->format('Y-m-d H:i:s');
        // dd($yesterday);
        $qb = $this->createQueryBuilder('a')
            ->select('a')
            ->where("a.card = :cardnumber and a.status = 'CAPTURED' and a.created BETWEEN '{$yesterday}' and '{$now}' ")
            ->groupBy('a.suyoolUserId')
            ->orderBy('a.created', 'DESC')
            ->setParameter('cardnumber', $cardnumber)
            ->getQuery()
            ->getResult();

        $QBaLL = $this->createQueryBuilder('a')
            ->select('a')
            ->where("a.card = :cardnumber and a.status = 'CAPTURED' and a.created BETWEEN '{$yesterday}' and '{$now}' ")
            ->setParameter('cardnumber', $cardnumber)
            ->getQuery()
            ->getResult();

        return array(count($qb),$QBaLL);
    }

    public function GetTransactionPerCardSum($cardnumber)
    {
        $now = new DateTime();
        $now = $now->format('Y-m-d H:i:s');
        $yesterday = new DateTime();
        $yesterday->modify('-1 day');
        $yesterday = $yesterday->format('Y-m-d H:i:s');
        return array($this->createQueryBuilder('a')
            ->select('sum(a.amount) as sum')
            ->where("a.card = :cardnumber and a.status = 'CAPTURED' and a.currency = 'USD' and a.created BETWEEN '{$yesterday}' and '{$now}'")
            ->setParameter('cardnumber', $cardnumber)
            ->getQuery()
            ->getSingleScalarResult(),$this->createQueryBuilder('a')
            ->select('a')
            ->where("a.card = :cardnumber and a.status = 'CAPTURED' and a.created BETWEEN '{$yesterday}' and '{$now}' ")
            ->setParameter('cardnumber', $cardnumber)
            ->getQuery()
            ->getResult());
    }

    public function getTransactionPerStatus($transid)
    {
        return $this->createQueryBuilder('a')
        ->select('a')
        ->where("a.transactionId = {$transid} and a.status is null")
        ->orderBy("a.id","DESC")
        ->setMaxResults(1)
        ->getQuery()
        ->getOneOrNullResult();
    }

    public function getTransactionPerStatusIfNotNull($transid)
    {
        return $this->createQueryBuilder('a')
        ->select('a')
        ->where("a.transactionId = {$transid} and a.status is not null")
        ->orderBy("a.id","DESC")
        ->setMaxResults(1)
        ->getQuery()
        ->getOneOrNullResult();
    }
}
