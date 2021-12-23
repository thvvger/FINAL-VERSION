<?php

namespace App\Form;

use App\Entity\AnnonceSonorise;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnnonceSonoriseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('annonce', null, [
                "attr" => [
                    "class" => "form-control "
                ]
            ])
            ->add('url_sono', null, [
                "attr" => [
                    "class" => "form-control "
                ]
            ])
            ->remove('reference', null, [
                "attr" => [
                    "class" => "form-control "
                ]
            ])
            ->remove('createdAt')
            ->remove('updatedAt')
            ->remove('deleted')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AnnonceSonorise::class,
        ]);
    }
}
