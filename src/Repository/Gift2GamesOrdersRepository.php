<?php

namespace App\Repository;

use App\Entity\Gift2Games\Order as Gift2GamesOrder;
use App\Entity\Gift2Games\Order;
use Doctrine\ORM\EntityRepository;


/**
 * @extends ServiceEntityRepository<Order>
 *
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class Gift2GamesOrdersRepository extends EntityRepository
{


    public function insertOrder($order)
    {
        $em = $this->getEntityManager();
        $em->persist($order);
        $em->flush();
    }

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
        $qb = $this->createQueryBuilder('o')
            ->select('count(o.id)')
            ->where("o.{$method} is not null")
            ->getQuery()
            ->getSingleScalarResult();

        return $qb;
    }

    public function getMethodPaidSum($method)
    {
        $qb = $this->createQueryBuilder('o')
            ->select('sum(o.amount)')
            ->where("o.{$method} is not null")
            ->getQuery()
            ->getSingleScalarResult();

        return $qb;
    }

    public function OrderSubscription($method = null, $searchQuery = null)
    {
        $where = "";
        if ($searchQuery != null) {
            if ($searchQuery['status'] != null && $searchQuery['amount'] != null && $searchQuery['transId'] != null) {
                $where = "where o.status='" . $searchQuery['status'] . "' and o.amount " . $searchQuery['amount'] . "and o.transId= " . $searchQuery['transId'];
            } else if ($searchQuery['status'] != null && $searchQuery['transId'] != null) {
                $where = "where o.status='" . $searchQuery['status'] . "' and o.transId=" . $searchQuery['transId'];
            } else if ($searchQuery['status'] != null && $searchQuery['amount'] != null) {
                $where = "where o.status='" . $searchQuery['status'] . "' and o.amount" . $searchQuery['amount'];
            } else if ($searchQuery['suyoolUserId'] != null) {
                $where = "where u.fname LIKE '%{$searchQuery['suyoolUserId']}%'";
            }else if ($searchQuery['status'] != null) {
                $where = "where o.status='" . $searchQuery['status'] . "'";
            } else if ($searchQuery['amount'] != null) {
                $where = "where o.amount " . $searchQuery['amount'];
            } else if ($searchQuery['transId'] != null) {
                $where = "where o.transId= " . $searchQuery['transId'];
            }
        }
        $connection = $this->getEntityManager()->getConnection();

        if ($method != null) {
            if ($searchQuery != null) {
                if ($searchQuery['status'] != null && $searchQuery['amount'] != null && $searchQuery['transId'] != null) {
                    $where = "and o.status='" . $searchQuery['status'] . "' and o.amount " . $searchQuery['amount'] . "and o.transId= " . $searchQuery['transId'];
                } else if ($searchQuery['status'] != null && $searchQuery['transId'] != null) {
                    $where = "and o.status='" . $searchQuery['status'] . "' and o.transId=" . $searchQuery['transId'];
                } else if ($searchQuery['status'] != null && $searchQuery['amount'] != null) {
                    $where = "and o.status='" . $searchQuery['status'] . "' and o.amount" . $searchQuery['amount'];
                }
                else if ($searchQuery['suyoolUserId'] != null) {
                    $where = "and u.fname LIKE '%{$searchQuery['suyoolUserId']}%'";
                }  else if ($searchQuery['status'] != null) {
                    $where = "and o.status='" . $searchQuery['status'] . "'";
                } else if ($searchQuery['amount'] != null) {
                    $where = "and o.amount " . $searchQuery['amount'];
                } else if ($searchQuery['transId'] != null) {
                    $where = "and o.transId= " . $searchQuery['transId'];
                }
            }
            $sql = "select
        o.id,o.suyoolUserId,u.fname,u.lname,o.status,o.amount,o.currency,o.transId,o.errorInfo as error,o.created
        FROM suyool_gift2games.orders o LEFT JOIN  suyool_notification.users u ON o.suyoolUserId = u.suyoolUserId
        WHERE o.{$method} is not null {$where}
        ORDER BY o.created DESC
         ";
        } else {
            $sql = "select
        o.id,o.suyoolUserId,u.fname,u.lname,o.status,o.amount,o.currency,o.transId,o.errorInfo as error,o.created
        FROM suyool_gift2games.orders o LEFT JOIN  suyool_notification.users u ON o.suyoolUserId = u.suyoolUserId
        {$where}
        ORDER BY o.created DESC
         ";
        }
        $stmt = $connection->prepare($sql);
        $result = $stmt->execute();
        $qb = $result->fetchAll();
        $array = array();
        foreach ($qb as $row) {
            if (!isset($array[$row['id']])) {
                $array[$row['id']] = ['id' => $row['id'], 'suyoolUserId' => $row['suyoolUserId'], 'fname' => $row['fname'], 'lname' => $row['lname'], 'status' => $row['status'], 'amount' => $row['amount'], 'currency' => $row['currency'], 'transId' => $row['transId'], 'error' => $row['error'], 'created' => $row['created']];
                if ($row['status'] != Gift2GamesOrder::$statusOrder['COMPLETED']) {
                    $array[$row['id']]['redFlag'] = true;
                }
            } else {
                if ($row['status'] != Gift2GamesOrder::$statusOrder['COMPLETED']) {
                    $array[$row['id']]['redFlag'] = true;
                }
            }
        }
        return array_merge($array);
    }
}
