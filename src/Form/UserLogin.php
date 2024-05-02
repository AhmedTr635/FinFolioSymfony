<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class UserLogin extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('email', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => "L'email est obligatoire",
                    ]),
                    new Assert\Email([
                        'message' => "L'email n'est pas valide ",
                    ])
                ],
            ])


            ->add('password', PasswordType::class, [
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
            ->add("Login",SubmitType::class)

        ;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
