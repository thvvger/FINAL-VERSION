<?php
/**
 * Created by PhpStorm.
 * User: LANGANFIN  Rogelio
 * Date: 23/05/2021
 * Time: 16:10
 */

namespace App\Form\User;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;


class UserProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', null, [
                "attr" => [
                    "class" => "form-control form-control-lg "
                ]
            ])
            ->add('prenom', null, [
                "attr" => [
                    "class" => "form-control form-control-lg "
                ]
            ])
            ->add('fonction', null, [
                "attr" => [
                    "class" => "form-control form-control-lg "
                ]
            ])
            ->add('imageFile', FileType::class, [
                "required" => false,
                "attr" => [
                    "class" => "form-control form-control-lg "
                ],
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/jpg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Merci uploader une image au format jpg ou jpeg',
                    ])
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}