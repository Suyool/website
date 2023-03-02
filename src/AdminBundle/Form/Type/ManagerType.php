<?php
namespace App\AdminBundle\Form\Type;

use App\Entity\Managers;
use Symfony\Component\Form\AbstractType;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ManagerType extends AbstractType
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('status', ChoiceType::class, [
                'label' => 'Status',
                'choices' => array_flip(Managers::$statusArray)
            ])
            ->add('accessLevel', ChoiceType::class, [
                'label' => 'Access level',
                'choices' => array_flip(Managers::$roles)
            ])
            ->add('name', TextType::class, [
                'label' => 'Name'
            ])
            ->add('user', TextType::class, [
                'label' => 'Username',
            ])
            ->add('email', TextType::class, [
                'label' => 'Email',
            ])
            ->add('phone', TextType::class, [
                'label' => 'Phone',
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options'  => ['label' => 'Password'],
                'second_options' => ['label' => 'Repeat Password']
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Create'
            ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'required' => false,
            'validation_groups' => function (FormInterface $form) {
                $data = $form->getData();

                if (!empty($data->getId())) {
                    return ['edit'];
                }

                return ['Default'];
            },
        ]);
    }
}