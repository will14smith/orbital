<?php

namespace AppBundle\Form\Type\Custom;

use AppBundle\Services\Enum\Gender;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GenderSelectType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => array_flip(Gender::$choices),
        ]);
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}