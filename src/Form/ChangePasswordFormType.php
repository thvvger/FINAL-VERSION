<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    "attr"=>[
                        "class"=>"form-control m-3",
                        "placeholder" =>"Nouveau mot de passe"
                    ],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez entrer un mot de passe',
                        ]),
                        new Length([
                            'min' => 4,
                            'minMessage' => 'Votre mot de passe doit être au moins {{ limit }} caractères',
                            // max length allowed by Symfony for security reasons
                            'max' => 8,
                        ]),
                    ],
                    'label' => false
                ],
                'second_options' => [
                    "attr"=>[
                        "class"=>"form-control m-3",
                        "placeholder" =>"Répéter le mot de passe"
                    ],
                    'label' => false,
                ],
                'invalid_message' => 'Les champs du mot de passe doivent correspondre.',
                // Instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
