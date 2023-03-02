<?php

// src/App/Form/Extension/ImageTypeExtension.php
namespace App\AdminBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Form\Extension\Core\Type\FormType;

class FormTypeExtension extends AbstractTypeExtension
{
    /**
     * Return the class of the type being extended.
     */
    public static function getExtendedTypes(): iterable
    {
        // return FormType::class to modify (nearly) every field in the system
        return [FormType::class];
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['locale'] = $options['locale'];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        // define locale option for each form field.
        // if null the labels are translates to current locale.
        $resolver->setDefaults(array('locale' => null));
    }

    public function getExtendedType()
    {
        return 'form';
    }
}