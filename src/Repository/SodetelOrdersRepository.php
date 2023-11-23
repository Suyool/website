<?php

namespace App\Repository;

use App\Entity\Sodetel\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<Order>
 *
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SodetelOrdersRepository extends EntityRepository
{
//    private $mr;
//    public function __construct(ManagerRegistry $registry)
//    {
//        parent::__construct($registry, Order::class);
//        $this->mr = $registry->getManager('sodetel');
//    }


//    public function insertOrder($order)
//    {
//        $this->mr->persist($order);
//        $this->mr->flush();
//    }
//
//    public function updateOrderStatus($orderId, $suyoolUserId, $prevStatus, $newStatus)
//    {
////        $order = $this->findOneBy(['id' => $orderId, 'suyoolUserId' => $suyoolUserId, 'status' => $prevStatus]);
//        $order = $this->findAll();
//
//        if ($order) {
//            $order->setStatus($newStatus);
//            dd($order);
//            $this->mr->persist($order);
//            $this->mr->flush();
//            return true;
//        }
//        return false;
//    }
//
//    public function updateOrderTransId($orderId, $transId, $status){
//        $order = $this->findOneBy(['id' => $orderId, 'status' => $status]);
//        if ($order) {
//            $order->setTransId($transId);
//            $this->mr->persist($order);
//            $this->mr->flush();
//            return true;
//        }
//        return false;
//    }

}
