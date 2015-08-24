<?php

namespace AppBundle\Form\Type\Custom;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoundSelectType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => 'AppBundle:Round'
        ]);
    }

    public function getParent()
    {
        return new SelectType(true);
    }

    public function getName()
    {
        return 'round';
    }
}