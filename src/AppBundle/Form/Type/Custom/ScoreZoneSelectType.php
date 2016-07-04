<?php

namespace AppBundle\Form\Type\Custom;

use AppBundle\Services\Enum\ScoreZones;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ScoreZoneSelectType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => array_flip(ScoreZones::$choices),
        ]);
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}
