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
use Symfony\Component\Validator\Constraints\IsTrue;
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
                        'pattern' => '/^(\+216)?[2459]\d{7}$/',
                        'message' => 'Entrez un numéro valide',
                    ]),
                ],
            ])
            ->add('password', PasswordType::class, [
                'attr' => [
                    'id' => 'user_password'
                ],
                'label' => 'Password',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => "Le mot de passe est obligatoire",
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[^\w\d\s:])([^\s]){8,}$/',
                        'message' => 'minimum 8 caractères, un majuscule, un minuscule, un chiffre et un caractère spécial',
                    ]),
                ],
            ])
            ->add('image', FileType::class, [
                'attr' => [
                    'id' => 'user_image'
                ],
                'label' => 'Choose an image',
                'required' => false
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
