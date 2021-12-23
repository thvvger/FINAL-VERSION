<?php

namespace App\Form;

use App\Entity\OrganisationSecteurActivite;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrganisationSecteurActiviteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('designation', null, [
                "attr" => [
                    "class" => "form-control form-control-lg "
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => OrganisationSecteurActivite::class,
        ]);
    }
}
