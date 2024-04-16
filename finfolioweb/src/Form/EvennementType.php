<?php

namespace App\Form;

use App\Entity\Evennement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
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
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Merci de saisir le nom de l evennement',
                    ]),
                    new Assert\Length([
                        'min' => 3,
                        'minMessage' => 'Le nom doit avoir au moins {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('montant', NumberType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Amount',
                'required' => false
            ])
            ->add('date', DateTimeType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Date',
                'widget' => 'single_text', // To use HTML5 date input
            ])
            ->add('adresse', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Address'
            ])
            ->add('description', TextareaType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Description'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Evennement::class,
        ]);
    }
}
