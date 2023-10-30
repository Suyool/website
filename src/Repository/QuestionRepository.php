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

    public function selectQuestionById($id){
        return $this->createQueryBuilder('q')
            ->where('q.id = :id and q.status != 0')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getQuestionsForNextCategory($nextCategory){
        return $this->createQueryBuilder('q')
            ->where('q.questionsCategory = :category and q.status != 0')
            ->setParameter('category', $nextCategory)
            ->setMaxResults(3) // Limit to 3 questions
            ->getQuery()
            ->getResult();
    }


    public function searchQuestions(string $searchString, bool $limited)
    {
        // SELECT * FROM `question` WHERE MATCH (question) AGAINST ('bank account' IN BOOLEAN MODE) > 0;
        $results = $this->createQueryBuilder('q')
            ->select('q.id', 'q.question', 'SUBSTRING(q.answer, 1, 150) AS answer', 'q.categoryId', 'c.image', 'c.name AS categoryName')
            ->leftJoin('q.questionsCategory','c')
            ->where('MATCH(q.question) AGAINST(:searchTerm IN BOOLEAN MODE) > 0 and q.status != 2')
            ->setParameter('searchTerm', $searchString)
            ->setMaxResults($limited ? 4 : 100)
            ->getQuery()
            ->getResult();

//        dd($results);

        return $results;
    }

}
