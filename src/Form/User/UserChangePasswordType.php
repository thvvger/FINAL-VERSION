<?php
/**
 * Created by PhpStorm.
 * User: LANGANFIN  Rogelio
 * Date: 23/05/2021
 * Time: 16:10
 */

namespace App\Form\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class UserChangePasswordType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('oldPassword', PasswordType::class, [
                'label' => 'Ancien mot de passe ',
                "attr" => [
                    "class" => "form-control form-control-lg  "
                ]
            ])
            ->add('newPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => false,
                'first_options' => ['label' => 'Nouveau mot de passe ', "attr" => [
                    "class" => "form-control form-control-lg "
                ]],
                'second_options' => ['label' => 'Confirmer le nouveau  mot de passe', "attr" => [
                    "class" => "form-control form-control-lg "
                ]],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => "App\Model\ChangePasswordModel",
        ]);
    }
}