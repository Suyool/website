<?php

namespace App\Form;

use App\Entity\Shopify\ShopifyInstallation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ShopifyInstallationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('domain')
            ->add('appKey')
            ->add('appPass')
            ->add('appSecret')
            ->add('shopCurrency')
            ->add('merchantId')
            ->add('certificateKey')
            ->add('integrationType')
            ->add('save', SubmitType::class, ['label' => 'Submit']); // Add the submit button

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ShopifyInstallation::class,
        ]);
    }
}
