<?php

namespace App\Repository;

use App\Entity\Touch\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityRepository;
use PDO;

/**
 * @extends ServiceEntityRepository<Plays>
 *
 * @method Plays|null find($id, $lockMode = null, $lockVersion = null)
 * @method Plays|null findOneBy(array $criteria, array $orderBy = null)
 * @method Plays[]    findAll()
 * @method Plays[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OgeroRepository extends EntityRepository
{
    public function getMethodPaid()
    {
        $qb = $this->createQueryBuilder('o')
            ->select('count(o.id)')
            ->where("o.landline is not null")
            ->getQuery()
            ->getSingleScalarResult();

        return $qb;
    }

    public function getMethodPaidSum()
    {
        $qb = $this->createQueryBuilder('o')
            ->select('sum(o.amount)')
            ->where("o.landline is not null")
            ->getQuery()
            ->getSingleScalarResult();

        return $qb;
    }

    
}
