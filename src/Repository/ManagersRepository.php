<?php

namespace App\Repository;

use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;

class ManagersRepository extends AppRepository implements UserLoaderInterface
{
    public function getManagers($predicates = false, $select = "m", $orderBy = false, $maxResults = false, $firstResult = false)
    {
        return $this->executeQuery("m",$predicates, $select, $orderBy, $maxResults, $firstResult);
    }

    public function getAllManagers(){
        return $this->buildQuery(
            'm',
            false,
            "m",
            ["m.createDate"=>"desc"]
        );
    }

    /**
     * @param int $authorId
     * @return Authors
     */
    public function findManagerById($authorId)
    {
        $manager = $this->getManagers(
            "m.id = $authorId",
            "m.id,m.user,m.name"
        );

        return !empty($manager)?$manager[0]:false;
    }

    public function loadUserByUsername($username)
    {
        return $this->createQueryBuilder('u')
            ->where("(u.user = '$username' OR u.email = '$username') and u.status = 1")
            ->getQuery()
            ->getOneOrNullResult();
    }
}