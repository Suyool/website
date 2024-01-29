<?php

namespace App\Form;

use App\Entity\Loto\order;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SearchPaymentForm extends AbstractType
{
    private $statusOrder = array('' => '', 'pending' => 'pending', 'completed' => 'completed', 'canceled' => 'canceled');

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('status', ChoiceType::class, [
                'choices' => $this->statusOrder,
                'label' => 'Status',
            ])
            ->add('transId', TextType::class, [
                'label' => 'TransId'
            ])
            ->add('currency', ChoiceType::class, [
                'choices'=>[
                    ''=>'',
                    'LBP'=>'LBP',
                    'USD'=>'USD'
                ],
                'label' => 'Currency'
            ])
            ->add('created',DateType::class,[
                'widget' => 'single_text',
                'required'=>true
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Search',
                'attr'=>['class'=>'btn btn-primary']
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
