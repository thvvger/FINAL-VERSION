<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', null, [
                "attr" => [
                    "class" => "form-control form-control-lg "
                ]
            ])
            ->add('userRole', null, [
                "placeholder" => "Sélectionner le role de l'utilisateur",
                "attr" => [
                    "class" => "form-control form-control-lg ",
                ]
            ])
            ->add('prenom', null, [
                "attr" => [
                    "class" => "form-control form-control-lg "
                ]
            ])
            ->add('email', EmailType::class, [
                "attr" => [
                    "class" => "form-control form-control-lg "
                ]
            ])
//            ->add('roles', ChoiceType::class, array(
//                'choices' => [
//                    'Admin' => 'ROLE_ADMIN',
//                    'Médécin' => 'ROLE_MEDECIN',
//                    'Sage femme' => 'ROLE_SAGE_FEMME',
//                    'Pharmacie' => 'ROLE_PHARMACIE',
//                    'Caisse' => 'ROLE_CAISSE',
//                ],
//
//                "attr" => [
//                    "class" => 'form-control form-control-lg'
//                ],
////                'label' => 'Roles',
////                'expanded' => true,
//                'multiple' => true,
//                'mapped' => true,
//            ))


            ->remove('password')
            ->add('telephone', null, [
                "attr" => [
                    "class" => "form-control form-control-lg "
                ]
            ])
//            ->add('fonction')
            ->add('fonction', null, [
                "attr" => [
                    "class" => "form-control form-control-lg "
                ]
            ])
//            ->add('is_active')
            ->add('is_active', ChoiceType::class, [
                'choices' => [
                    'Actif' => true,
                    'Non actif' => false,
                ],
                "attr" => [
                    "class" => "form-control form-control-lg "
                ]
            ])
            ->add('isOrganisationAdmin', CheckboxType::class, [
//                'label' => 'Show this entry publicly?',
                'label' => "Administrateur d'organisation ",
                'required' => false,
            ])
            ->add('organisation', null, [
                "attr" => [
                    "class" => "form-control form-control-lg "
                ]
            ])
            ->add('imageFile', FileType::class, [
                "required" =>false,
                "attr" => [
                    "class" => "form-control form-control-lg "
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
