<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use App\Entity\Authors;
use Doctrine\ORM\Query\Expr\Join;

class AppRepository extends EntityRepository
{
    /**
     * @param string|false $predicates
     * @param string $select
     * @param array|false $orderBy
     * @param int|false $maxResults
     * @param int|false $firstResult
     */
    public function initQuery($alias, $predicates = false, $select, $orderBy = false, $maxResults = false, $firstResult = false, $groupBy = false, $indexBy = false)
    {
        $qb = $this->createQueryBuilder($alias)
            ->select($select);

        if ($predicates) $qb->where($predicates);
        if ($orderBy) {
            foreach ($orderBy as $sort => $order) {
                $qb->addOrderBy($sort, $order);
            }
        }
        if ($maxResults) $qb->setMaxResults($maxResults);
        if ($firstResult) $qb->setFirstResult($firstResult);
        if ($groupBy) $qb->groupBy($groupBy);
        if ($indexBy) $qb->indexBy($alias, $indexBy);

        return $qb;
    }

    public function buildQuery($alias, $predicates = false, $select, $orderBy = false, $maxResults = false, $firstResult = false, $groupBy = false, $indexBy = false)
    {
        return $this->initQuery($alias, $predicates, $select, $orderBy, $maxResults, $firstResult, $groupBy, $indexBy)
                    ->getQuery()->useQueryCache(true);
    }

    public function executeQuery($alias, $predicates = false, $select, $orderBy = false, $maxResults = false, $firstResult = false, $groupBy = false, $indexBy = false)
    {
        $dataArray = $this->buildQuery($alias, $predicates, $select, $orderBy, $maxResults, $firstResult, $groupBy, $indexBy)
            ->getScalarResult();

        $object = null;
        eval('$object = '."new " . $this->getClassName() . "();");

        return (!is_null($object))?$object->convertToObject($dataArray):false;
    }
}