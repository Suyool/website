<?php

namespace App\Form;

use App\Entity\Loto\order;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SearchAlfaOrdersForm extends AbstractType
{
    private $amount = array('ALL' => '', 'less than 500 thousands' => '< 500000', 'greater than million' => '>1000000');

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
            ->add('transId',IntegerType::class,[
                'label'=>'Transaction Id'
            ])
            ->add('suyoolUserId',TextType::class,[
                'label'=>'Suyooler name'
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
            'method'=>'get'
        ]);
    }
}
