<?php

namespace App\Form;

use App\Entity\DigitalCoins;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DigitalCoinsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('recentValue')
            ->add('dateAchat')
            ->add('dateVente')
            ->add('montant')
            ->add('leverage')
            ->add('stopLoss')
            ->add('userId')
            ->add('ROI')
            ->add('prixAchat')
            ->add('tax')
            ->add('code')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DigitalCoins::class,
        ]);
    }
}
