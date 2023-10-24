<?php

namespace App\Repository;

use App\Entity\Alfa\Order as AlfaOrder;
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
class AlfaOrdersRepository extends EntityRepository
{
    public function CountStatusOrders()
    {
        $queryResult = $this->createQueryBuilder('o')
            ->select("o.status, COUNT(o) AS statuscount")
            ->groupBy('o.status')
            ->getQuery()
            ->getResult();
        $resultArray = [];
        foreach ($queryResult as $row) {
            $resultArray[$row['status']] = $row['statuscount'];
        }

        return $resultArray;
    }

    public function getMethodPaid($method)
    {
        $qb=$this->createQueryBuilder('o')
        ->select('count(o.id)')
        ->where("o.{$method} is not null")
        ->getQuery()
        ->getSingleScalarResult();

        return $qb;
    }

    public function getMethodPaidSum($method)
    {
        $qb=$this->createQueryBuilder('o')
        ->select('sum(o.amount)')
        ->where("o.{$method} is not null")
        ->getQuery()
        ->getSingleScalarResult();

        return $qb;
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
        o.id,o.suyoolUserId,u.fname,u.lname,o.status,o.amount,o.currency,o.transId,o.errorInfo as error,o.create_date,o.postpaid_id,o.prepaid_id
        FROM suyool_alfa.orders o LEFT JOIN  suyool_notification.users u ON o.suyoolUserId = u.suyoolUserId
        ORDER BY o.create_date DESC
         ";

        $stmt = $connection->prepare($sql);
        $result = $stmt->execute();
        $qb = $result->fetchAll();

        $array = array();

        foreach ($qb as $row) {
            if (!isset($array[$row['id']])) {
                $array[$row['id']] = ['id' => $row['id'], 'suyoolUserId' => $row['suyoolUserId'], 'fname' => $row['fname'], 'lname' => $row['lname'], 'status' => $row['status'], 'amount' => $row['amount'], 'currency' => $row['currency'], 'transId' => $row['transId'], 'error' => $row['error'], 'created' => $row['create_date']];
                if ($row['status'] != AlfaOrder::$statusOrder['COMPLETED']) {
                    $array[$row['id']]['redFlag'] = true;
                }
            } else {
                if ($row['status'] != AlfaOrder::$statusOrder['COMPLETED']) {
                    $array[$row['id']]['redFlag'] = true;
                }
            }
        }

        return array_merge($array);
    }
}
