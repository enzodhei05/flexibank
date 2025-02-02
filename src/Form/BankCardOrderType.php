<?php

namespace App\Form;

use App\Entity\CardOrder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BankCardOrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('customerName', TextType::class, [
                'label' => 'Nom du client',
                'required' => true,
            ])
            ->add('customerEmail', EmailType::class, [
                'label' => 'Email du client',
                'required' => true,
                'disabled' => true, // Désactive le champ pour éviter la modification
            ])
            ->add('cardCategory', ChoiceType::class, [
                'label' => 'Catégorie de carte',
                'choices' => [
                    'Classique' => 'classique',
                    'Premium' => 'premium',
                    'Gold' => 'gold',
                ],
                'required' => true,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Commander',
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CardOrder::class,
        ]);
    }
}
