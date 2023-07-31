<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class NotificationRepository extends EntityRepository
{
    public function truncate()
    {
        return $this->createQueryBuilder('n')
            ->delete()
            ->getQuery()
            ->execute();
    }
}
