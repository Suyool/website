<?php

namespace App\AdminBundle\Form\DataTransformer;

use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Tags;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class TimestampTransformer implements DataTransformerInterface
{
    public function transform($timestamp)
    {
        $dt = new \DateTime();
        $dt->setTimestamp($timestamp);
        return $dt;
    }

    public function reverseTransform($date)
    {
        return $date->getTimestamp();
    }
}