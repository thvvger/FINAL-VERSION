<?php
/**
 * Created by PhpStorm.
 * User: LANGANFIN  Rogelio
 * Date: 11/04/2021
 * Time: 19:54
 */

namespace App\Form\Partial\Organisation;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class _LimiteRequeteType  extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            "placeholder"=>"choisr ...",
            'choices' => [
                '500' => '500',
                '1000' => '1000',
                '1500' => '1500',
                'Illimite' => '999999999',
            ],
            'expanded' => false,
            "attr"=>['class'=>"form-control"]
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}