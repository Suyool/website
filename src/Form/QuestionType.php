<?php

// src/Form/QuestionType.php

namespace App\Form;

use App\Entity\Question;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('question')
            ->add('answer', CKEditorType::class, [
                'config' => [
                    'toolbar' => 'full',
                ],
            ])
            ->add('questionsCategory', EntityType::class, [
                'class' => 'App\Entity\QuestionsCategory',
                'query_builder' => function (EntityRepository $en) {
                    return $en->createQueryBuilder('c')
                        ->orderBy('c.name', 'ASC');
                },
                'choice_label' => 'name', // Display category names in the dropdown
                'placeholder' => 'Select a Category', // Optional placeholder text
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
        ]);
    }
}
