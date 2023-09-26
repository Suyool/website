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


//    public function findQuestionsByCategories( string $type=""){
//        $data = $this->createQueryBuilder('q')
//            ->select('q.id', 'q.question', 'q.answer', 'c.name')
//            ->from('App\Entity\QuestionsCategory', 'c')
//            ->leftJoin('c.questions', 'q')
//            ->getQuery()
//            ->getResult();
//
//        dd($data);
//}
    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function findQuestionsByCategories(string $type="")
    {
        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.questions', 'q') // Assuming the association is named 'questions'
            ->addSelect('q') // Include the questions in the result
            ->getQuery();
        return $qb->getResult();
    }



// /**
//  * @return QuestionsCategory[] Returns an array of QuestionsCategory objects
//  */
/*
public function findByExampleField($value)
{
    return $this->createQueryBuilder('q')
        ->andWhere('q.exampleField = :val')
        ->setParameter('val', $value)
        ->orderBy('q.id', 'ASC')
        ->setMaxResults(10)
        ->getQuery()
        ->getResult()
    ;
}
*/

/*
public function findOneBySomeField($value): ?QuestionsCategory
{
    return $this->createQueryBuilder('q')
        ->andWhere('q.exampleField = :val')
        ->setParameter('val', $value)
        ->getQuery()
        ->getOneOrNullResult()
    ;
}
*/
}
