<?php

namespace App\Repository;

use App\Entity\Orders;
use Doctrine\Persistence\ManagerRegistry;

class OrdersRepository extends AppRepository
{
    public function __construct()
    {
    }

    // Add custom repository methods here
    // For example, you can define queries or additional find methods

    // Example custom method: Find orders by status
    public function findByStatus(string $status): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.status = :status')
            ->setParameter('status', $status)
            ->getQuery()
            ->getResult();
    }
}
