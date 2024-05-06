<?php

namespace App\Form;

use App\Entity\ActifNonCourant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ActifNonCourantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'Logiciel' => 'Logiciel',
                    'Materiel' => 'Materiel',
                    'Actif Biologique' => 'Actif Biologique',

                    'Autre' => 'Autre',
                ],
                'placeholder' => 'Choisir un type',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez choisir un type.',
                    ]),
                ],
            ])
            ->add('valeur')
            ->add('prix_achat')
            ->add('user_id')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ActifNonCourant::class,
        ]);
    }
}
