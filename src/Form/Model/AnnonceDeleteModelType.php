<?php

namespace App\Form\Model;

use App\Entity\Annonce;
use App\Entity\Organisation;
use App\Model\AnnonceDeleteModel;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnnonceDeleteModelType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('organisation', EntityType::class, [
                "class"=> Organisation::class,
                "attr" => [
                    "class" => "form-control form-control-lg "
                ],
                'placeholder' => "Choisisser l'organisation"
            ])
             ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AnnonceDeleteModel::class ,
        ]);
    }
}
