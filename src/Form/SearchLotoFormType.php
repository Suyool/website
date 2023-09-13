<?php

namespace App\Form;

use App\Entity\Loto\order;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;


class SearchLotoFormType extends AbstractType
{
    private $amount = array('ALL' => '', 'less than 100 thousands' => '< 100000', 'greater than 100' => '>100000', 'equal 100' => '=100000');

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('status', ChoiceType::class, [
                'choices' => order::$statusOrder,
                'label' => 'Status',
            ])
            ->add('amount', ChoiceType::class, [
                'choices' => $this->amount,
                'label' => 'Amount'
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Search',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'required' => false,
            'csrf_protection'   => false,
            'method' => 'get'
        ]);
    }
}
