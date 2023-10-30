<?php

namespace App\Repository;

use App\Entity\Plays;
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
class SupportRepository extends EntityRepository
{

    public function CountSupports()
    {
        return $this->createQueryBuilder('s')
        ->select('count(s)')
        ->getQuery()
        ->getSingleScalarResult();
    }

}
