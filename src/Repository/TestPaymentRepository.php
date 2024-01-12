<?php

namespace App\Repository;

use App\Entity\topup\testbob_transactions;
use App\Entity\topup\session;
use DateTime;
use Doctrine\ORM\EntityRepository;

class TestPaymentRepository extends EntityRepository
{


    public function findTestTransactionsThatIsNotCompleted($transId)
    {
        return $this->createQueryBuilder('o')
        ->select('o')
        ->where("o.status != 'completed' and o.transId = {$transId}")
        ->orderBy('o.created','DESC')
        ->setMaxResults(1)
        ->getQuery()
        ->getOneOrNullResult();
    }
}
