<?php

namespace App\Form;

use App\Entity\Managers;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchUsersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Username',
                ],
            ])
            ->add('role', ChoiceType::class, [
                'label' => false,
                'choices' => array_flip(User::$UsersStatusArray),
                'attr' => [
                    'placeholder' => 'Access Level',
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'SEARCH',
                'attr' => [
                    'class' => 'btn-primary tags-btn',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'required' => false,
            'validation_groups' => false,
            'csrf_protection'   => false,
            'method' => 'get',
            'locale' => 'en',
        ]);
    }
}
