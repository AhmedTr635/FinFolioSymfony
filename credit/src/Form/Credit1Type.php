<?php

namespace App\Form;

use App\Entity\Credit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Validator\Constraints as Assert;


class Credit1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('montant', TextType::class, [
                'required' => false,
                'constraints' => [
                    // Allow null or string types
                ],
            ])
            ->add('interetMax', null, [
                'required' => false,
                'constraints' => [

                ],
            ])
            ->add('interetMin', null, [
                'required' => false,
                'constraints' => [

                ],
            ])
            ->add('dateD', DateType::class, [
                'required' => false,
                'widget' => 'single_text',
                'html5' => true,
                'format' => 'yyyy-MM-dd',
                'empty_data' => null,
                'constraints' => [

                ],
            ])
            ->add('dateF', DateType::class, [
                'required' => false,
                'widget' => 'single_text',
                'html5' => true,
                'format' => 'yyyy-MM-dd',
                'empty_data' => null,
                'constraints' => [

                ],
            ])
            ->add('user_id')
        ;
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Credit::class,
        ]);
    }
}
