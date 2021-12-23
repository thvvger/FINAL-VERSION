<?php

namespace App\Form;

use App\Entity\Annonce;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnnonceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('organisation', null, [
                "attr" => [
                    "class" => "form-control form-control-lg "
                ],
                'placeholder' => "Choisisser l'employeur"
            ])
            ->add('reference', null, [
                'label' => " Reference de l'annonce",
                "attr" => [
                    "class" => "form-control form-control-lg "
                ]
            ])
            ->add('dateExpiration', TextType::class, [
                'label' => "Date d'expiration",
                "attr" => [
                    "class" => "form-control form-control-lg dateTimePicker"
                ]
            ])
            ->add('titre', null, [
                'label' => " Titre de l'annonce",
                "attr" => [
                    "class" => "form-control form-control-lg "
                ]
            ])
            ->add('typeContrat', null, [
                'label' => "Type de contrat",
                "attr" => [
                    "class" => "form-control form-control-lg "
                ]
            ])
            ->add('niveauEtude', null, [
                'label' => "Niveau d'étude",
                "attr" => [
                    "class" => "form-control form-control-lg "
                ]
            ])
            ->add('lieu', null, [
                'label' => "Lieu",
                "attr" => [
                    "class" => "form-control form-control-lg "
                ]
            ])
            ->add('remuneration', null, [
                'label' => "Rémuneration",
                "attr" => [
                    "class" => "form-control form-control-lg "
                ]
            ])
            ->add('salaire', null, [
                'label' => "Salire",
                "attr" => [
                    "class" => "form-control form-control-lg "
                ]
            ])
            ->add('description', null, [
                "attr" => [
                    "class" => "form-control form-control-lg "
                ]
            ])
            ->add('profil_recherche', null, [
                "attr" => [
                    "class" => "form-control form-control-lg "
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Annonce::class,
        ]);
    }
}
