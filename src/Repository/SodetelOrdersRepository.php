<?php

namespace App\Repository;

use App\Entity\Sodetel\Order as SodetelOrder;
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
class SodetelOrdersRepository extends EntityRepository
{
    public function saveOrder(SodetelOrder $order)
    {
        try {
            $this->_em->persist($order);
            $this->_em->flush();
        } catch (OptimisticLockException | ORMException $e) {
            return false;
        }
        return true;
    }

}
