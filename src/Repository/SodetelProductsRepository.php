<?php

namespace App\Repository;

use App\Entity\Sodetel\Product;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SodetelProductsRepository extends EntityRepository
{
//    private $mr;
//    public function __construct(ManagerRegistry $registry)
//    {
//        parent::__construct($registry, Product::class);
//        $this->mr = $registry->getManager('sodetel');
//    }


//    public function insertProduct($product)
//    {
//        $this->mr->persist($product);
//        $this->mr->flush();
//    }

}
