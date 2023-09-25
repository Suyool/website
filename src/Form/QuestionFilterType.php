<?php
namespace App\Form;

// src/Form/QuestionFilterType.php

use App\Repository\QuestionsCategoryRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class QuestionFilterType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category', EntityType::class, [
                'class' => 'App\Entity\QuestionsCategory',
                'query_builder' => function (EntityRepository $en) {
                    return $en->createQueryBuilder('c')
                        ->orderBy('c.name', 'ASC');
                },
                'choice_label' => 'name',
                'placeholder' => 'Select a Category',
                'label' => false,
                'required' => false, // Make the field not required
            ]);
    }
}
