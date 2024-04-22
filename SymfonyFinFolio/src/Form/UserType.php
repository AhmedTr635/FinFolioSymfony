<?php

namespace App\Form;

use App\Entity\User;
use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;



class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('nom', TextType::class, [
                'attr' => [
                    'id' => 'user_nom'
                ],

            ])
            ->add('prenom', TextType::class, [
                'attr' => [
                    'id' => 'user_prenom'
                ],

            ])
            ->add('email', TextType::class, [
                'attr' => [
                    'id' => 'user_email'
                ],

            ])
            ->add('numtel', TextType::class, [
                'attr' => [
                    'id' => 'user_numtel'
                ],

            ])
            ->add('password', PasswordType::class, [
                'attr' => [
                    'id' => 'user_password'
                ],
                'label' => 'Password',

            ])

            ->add('image', FileType::class, [
                'label' => 'Votre image de profil (Des fichiers images uniquement)',
                // unmapped means that this field is not associated to any entity property
                'mapped' => false,
                'attr' => [
                    'id' => 'user_image'
                ],
                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,
                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez sélectionner une image.',
                    ]),
                    new File([
                        'maxSize' => '2048k',
                        'mimeTypes' => [
                            'image/gif',
                            'image/jpeg',
                            'image/png',
                            'image/jpg',
                        ],

                        'mimeTypesMessage' => 'Please upload a valid Image',
                    ])
                ],
            ])


            ->add('Enregistrer', SubmitType::class, [
                'attr' => [
                    'id' => 'user_submit'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
