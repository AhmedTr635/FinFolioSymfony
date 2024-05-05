<?php

namespace App\Form;

use App\Entity\RealEstate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RealEstateType extends AbstractType
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
            ->add('virtualTourLink', UrlType::class, [ // Add this line
                'label' => 'Virtual Tour Link',
                'required' => false,
            ])
            ->add('imageData', FileType::class, [
                'label' => 'Image (JPEG, PNG, GIF)',
                'required' => false,
                'mapped' => false, // This field is not mapped to a property of the entity
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RealEstate::class,
        ]);
    }
}