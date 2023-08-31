<?php

namespace App\Form;

use App\Entity\Support;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;

class SupportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Please enter your name.']),
                ],
            ])
            ->add('mail', null, [
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Please enter your email.']),
                    new Email(['message' => 'Please enter a valid email address.']),
                ],
            ])
            ->add('subject', null, [
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Please enter a subject.']),
                ],
            ])
            ->add('message', null, [
                'required' => true,
                'constraints' => [
                    new NotBlank(['message' => 'Please enter a message.']),
                    new Length([
                        'min' => 10,
                        'minMessage' => 'Your message should be at least {{ limit }} characters long.',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Support::class,
        ]);
    }
}

