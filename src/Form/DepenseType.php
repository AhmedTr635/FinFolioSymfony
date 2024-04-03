<?php

namespace App\Form;

use App\Entity\Depense;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class DepenseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder



            ->add('montant',NumberType ::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Merci de saisir votre montant',
                    ]),
//                    new Assert\Length([
//                        'min' => 3,
//                        'minMessage' => 'Le montant doit avoir au moins {{ limit }} caractères',
//                    ]),
                    new Assert\Positive([
                        'message' => 'Le montant doit être positif.',
                    ]),
                ],
            ])
            ->add('type', TextType::class, [
                'attr' =>['class' => 'form-control',
],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Merci de saisir votre type',
                    ]),


                ],
            ])


            ->add('date', DateType::class, [
                'widget' => 'single_text',


//                'constraints' => [
//                    new Assert\NotBlank([
//                        'message' => 'Please enter the date.',
//                    ]),
//                    new Assert\Date([
//                        'message' => 'entrer une date .',
//                    ]),
//                    // You can add additional constraints here if needed
//                ],
//                 Add any additional date options as needed
            ]);


    }



    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Depense::class,
        ]);
    }
}
