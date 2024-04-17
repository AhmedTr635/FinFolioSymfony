<?php

namespace App\Form;

use App\Entity\User;
use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\NotBlank;



class UserModifType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('statut', ChoiceType::class, [
                'choices' => [
                    'Active' => 'active',
                    'Desactive' => 'desactive',
                    'ban' => 'ban',
                ],
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'user_statut', // Attribuer un ID à ce champ
                ],
                'label' => 'Statut',
            ])
            ->add('datepunition', DateType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'datepicker',
                    'id' => 'user_datepunition', // Attribuer un ID à ce champ
                ],
                'label' => 'Date de Punition',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir une date de punition.',
                    ]),
                ],





            ])
            ->add('Enregistrer', SubmitType::class, [
                'attr' => [
                    'id' => 'user_modif_submit'
                ]
            ])
        ;

    }
    // Méthode de validation
// Méthode de validation



    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
