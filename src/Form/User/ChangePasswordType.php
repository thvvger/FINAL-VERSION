<?php

namespace App\Form\User;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangePasswordType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('organisation', null, [
                "attr" => [
                    "class" => "form-control form-control-lg "
                ]
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'required'=>false,
                'first_options' => ['label' => 'Nouveau mot de passe '],
                'second_options' => ['label' => 'Confirmer le mot de passe'],
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
                'data_class' => User::class,
                'validation_groups' => ['Default', 'PasswordReset']
            ]
        );
    }
}
