<?php
/**
 * Created by PhpStorm.
 * User: LANGANFIN  Rogelio
 * Date: 18/03/2021
 * Time: 22:48
 */

namespace App\Form\Partial\Organisation;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class _TypeBoutonType  extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => [
                'Lecteur audio' => 'audio',
                'Pop-up(modal)' => 'modal',
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