<?php

namespace App\Repository;

use App\Entity\QuestionsCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<QuestionsCategory>
 *
 * @method QuestionsCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method QuestionsCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method QuestionsCategory[]    findAll()
 * @method QuestionsCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionsCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuestionsCategory::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(QuestionsCategory $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(QuestionsCategory $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }


    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function findQuestionsByCategories(int $type = 1)
    {
        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.questions', 'q')
            ->addSelect('q')
            ->where('c.type = ' . $type . 'and q.status != 0')
            ->getQuery();
        return $qb->getResult();
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function getNextCategories($id, $type)
    {
        $qb = $this->createQueryBuilder('c')
            ->where('c.id > :categoryId') // Select categories with IDs greater than the current category's ID
            ->andWhere('c.type = :type') // Add the condition for type
            ->setParameter('categoryId', $id)
            ->setParameter('type', $type) // Bind the type parameter
            ->orderBy('c.id', 'ASC') // Order by category ID in ascending order
            ->setMaxResults(3) // Limit to 3 categories
            ->getQuery()
            ->getResult();

        return $qb;
    }


}
