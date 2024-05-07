<?php

namespace App\Form;

use App\Entity\RealEstate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RealEstate1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('emplacement')
            ->add('ROI')
            ->add('valeur')
            ->add('nbrchambres')
            ->add('superficie')
            ->add('nbrclick')
            ->add('imageData')
            ->add('virtualTourLink')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RealEstate::class,
        ]);
    }
}
