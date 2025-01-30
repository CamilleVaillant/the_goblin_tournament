<?php

namespace App\Form;

use App\Entity\Tournament;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class,[
                'attr' => [
                    'placeholder' => 'Entrez votre nom ici...'
                ]
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Choisissez un mot de passe :',
                'attr' => [
                    'placeholder' => 'Entrez votre mot de passe ici...'
                ]
            ])
            ->add('pseudo', TextType::class,[
                'attr' => [
                    'placeholder' => 'Choisisez votre peusdo ici...'
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
