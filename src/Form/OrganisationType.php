<?php

namespace App\Form;

use App\Entity\Organisation;
use App\Entity\OrganisationSecteurActivite;
use App\Form\Partial\Organisation\_LimiteRequeteType;
use App\Form\Partial\Organisation\_TypeBoutonType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class OrganisationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('raison_social',null,[
                   "attr"=>[
                    "class"=>"form-control form-control-lg "
                ]
            ])
            ->remove('num_entreprise',null,[
                   "attr"=>[
                    "class"=>"form-control form-control-lg "
                ]
            ])
            ->add('secteurActivite',EntityType::class,[
                "placeholder"=>"Choisir...",
                "class"=>OrganisationSecteurActivite::class,
                   "attr"=>[
                    "class"=>"form-control form-control-lg "
                ]
            ])
            ->add('url',null,[
                   "attr"=>[
                    "class"=>"form-control form-control-lg "
                ]
            ])
            ->add('typeSonorisation', ChoiceType::class, [
                'choices'  => [
                    'Sonorisation automatique' => "automatique",
                    'Sonorisation manuelle' => "manuelle",
                ],
                "attr"=>[
                    "class"=>"form-control form-control-lg "
                ]
            ])
//            ->add('sonorisationAutomatique' )
            ->add('limite_requete',_LimiteRequeteType::class,[
                   "attr"=>[
                    "class"=>"form-control form-control-lg "
                ]
            ])
            ->add('selecteur',null,[
                   "attr"=>[
                    "class"=>"form-control form-control-lg "
                ]
            ])
            ->add('apiLink',null,[
                   "attr"=>[
                    "class"=>"form-control form-control-lg "
                ]
            ])
            ->add('type_bouton',_TypeBoutonType::class,[
                   "attr"=>[
                    "class"=>"form-control form-control-lg "
                ]
            ])

            ->add('nombreUtilisateur',null,[
                   "attr"=>[
                    "class"=>"form-control form-control-lg "
                ]
            ])
            ->add('logo_image',FileType::class,[
                   "attr"=>[
                    "class"=>"form-control form-control-lg "
                ]
            ])
            ->add('logo_image',FileType::class,[
                   "attr"=>[
                    "class"=>"form-control form-control-lg "
                ]
            ])
            ->add('biographie',null,[
                   "attr"=>[
                    "class"=>"form-control form-control-lg "
                ]
            ])

            ->remove('imageFile', FileType::class, [
                'required' => false,
                "attr" => [
                    "class" => "form-control form-control-lg "
                ]
            ])
            ->add('backgroundImageFile', FileType::class, [
                'required' => false,
                "attr" => [
                    "class" => "form-control form-control-lg "
                ]
            ])
            ->remove('logo_image')

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Organisation::class,
        ]);
    }
}
