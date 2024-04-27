<?php

namespace App\Form;

use App\Entity\Evennement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;


class EvennementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom_event', TextType::class, [
                'attr' => [
                    'id' => 'event_nom'
                ],

            ])
            ->add('montant', NumberType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Montant',
                'required' => false
            ])
            ->add('date', DateTimeType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Date',
                'widget' => 'single_text', // To use HTML5 date input
                'required' => true,
            ])
            ->add('adresse', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Address'
            ])
            ->add('description', TextareaType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Description'
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
            'data_class' => Evennement::class,
        ]);
    }
}
