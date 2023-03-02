<?php

namespace App\EventListener;

use App\Entity\News;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SearchIndexer
{
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        //   $entity->fillEmptyValues();

        $user = false;
        $userId = false;
        if(is_object($this->tokenStorage->getToken()) && is_object($this->tokenStorage->getToken()->getUser())) {
            $user = $this->tokenStorage->getToken()->getUser();
            $userId = $user->getId();
        }

        if(!$entity->getId()) {
            if($userId){
                $entity->setCreatorData($user);
                $entity->setCreatorIP($_SERVER['REMOTE_ADDR']);
            }

            $entity->setCreateDate(new \DateTime());
        }
        
        if($userId){
            $entity->setUpdator($userId);
            $entity->setUpdatorIp($_SERVER['REMOTE_ADDR']);
        }

        $entity->setUpdateDate(new \DateTime());

        /*       if (!$entity instanceof Product) {
                   return;
               }

               $entityManager = $args->getObjectManager();*/
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        //   $entity->fillEmptyValues();

        $userId = false;
        if(is_object($this->tokenStorage->getToken()) && is_object($this->tokenStorage->getToken()->getUser())) {
            $userId = $this->tokenStorage->getToken()->getUser()->getId();
        }
        
        if($userId){
            $entity->setUpdator($userId);
            $entity->setUpdatorIp($_SERVER['REMOTE_ADDR']);
        }

        $entity->setUpdateDate(new \DateTime());

        /*       if (!$entity instanceof Product) {
                   return;
               }

               $entityManager = $args->getObjectManager();*/
    }
}