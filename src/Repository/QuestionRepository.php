<?php

namespace App\Repository;

use App\Entity\Question;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Question>
 *
 * @method Question|null find($id, $lockMode = null, $lockVersion = null)
 * @method Question|null findOneBy(array $criteria, array $orderBy = null)
 * @method Question[]    findAll()
 * @method Question[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Question::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Question $entity, bool $flush = true): void
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
    public function remove(Question $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }


    public function getQuestionsForNextCategory($nextCategory){
        return $this->createQueryBuilder('q')
            ->where('q.questionsCategory = :category')
            ->setParameter('category', $nextCategory)
            ->setMaxResults(3) // Limit to 3 questions
            ->getQuery()
            ->getResult();
    }


    public function searchQuestions(string $searchString, bool $limited)
    {
        // SELECT * FROM `question` WHERE MATCH (question) AGAINST ('bank account' IN BOOLEAN MODE) > 0;
        $results = $this->createQueryBuilder('q')
            ->where('MATCH(q.question) AGAINST(:searchTerm IN BOOLEAN MODE) > 0')
            ->setParameter('searchTerm', $searchString)
            ->setMaxResults($limited? 4 : 100)
            ->getQuery()
            ->getResult();

        return $results;
    }

}
