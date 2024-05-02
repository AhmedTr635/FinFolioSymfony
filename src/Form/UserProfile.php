<?php

namespace App\Form;

use App\Entity\User;
use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;


class UserProfile extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'attr' => [
                    'id' => 'user_nom'
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Merci de saisir votre nom',
                    ]),
                    new Assert\Length([
                        'min' => 3,
                        'minMessage' => 'Le nom doit avoir au moins {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('prenom', TextType::class, [
                'attr' => [
                    'id' => 'user_prenom'
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Merci de saisir votre prénom',
                    ]),
                    new Assert\Length([
                        'min' => 3,
                        'minMessage' => 'Le prénom doit avoir au moins {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('email', TextType::class, [
                'attr' => [
                    'id' => 'user_email'
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => "L'email est obligatoire",
                    ]),
                    new Assert\Email([
                        'message' => "L'email n'est pas valide ",
                    ])
                ],
            ])
            ->add('numtel', TextType::class, [
                'attr' => [
                    'id' => 'user_numtel'
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Merci de saisir votre numéro',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^[2459]\d{7}$/',
                        'message' => 'Entrez un numéro valide',
                    ]),
                ],
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
