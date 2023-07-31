<?php


namespace App\EventListener;

use App\Entity\News;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SearchIndexer
{
    public function __construct()
    {
    }

    // public function prePersist(LifecycleEventArgs $args)
    // {
    //     $entity = $args->getObject();

    //     if(!$entity->getId()) {
    //         $entity->setCreateDate(new \DateTime());
    //     }

    //     $entity->setUpdateDate(new \DateTime());
    // }

    // public function preUpdate(LifecycleEventArgs $args)
    // {
    //     $entity = $args->getObject();

    //     $entity->setUpdateDate(new \DateTime());
    // }
}