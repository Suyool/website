<?php
// src/Form/QuestionsCategoryType.php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\QuestionsCategory;

class QuestionsCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Category Name',
                'required' => true,
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Category Status',
                'choices' => [
                    'Active' => 1,
                    'Inactive' => 0,
                ],
                'required' => true,
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Category Status',
                'choices' => [
                    'Personal' => 1,
                    'Corporate' => 2,
                ],
                'required' => true,
            ]);;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => QuestionsCategory::class,
        ]);
    }
}

