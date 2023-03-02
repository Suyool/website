<?php
namespace App\AdminBundle\Form\Type;

use App\Entity\Categories;
use App\Entity\Livenews;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ThresholdType extends AbstractType
{
    private $translator;
    protected $em;

    public function __construct(TranslatorInterface $translator, EntityManagerInterface $em)
    {
        $this->translator = $translator;
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('threshold', TextType::class, [
                'label' => 'Threshold',
                'attr'=>[
                    'required'=>true
                ]
            ])
            ->add('save', SubmitType::class, [
                'label' => "Save",
                'attr'=>[
                    'class'=>'btn btn-primary btn-sm'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'required' => false,
            'attr'=>[
                'class'=>'form-inline'
            ]
        ]);
    }
}