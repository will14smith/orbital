<?php

namespace AppBundle\Form\Type\Custom;

use AppBundle\Services\Enum\BowType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BowTypeSelectType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => BowType::$choices,
        ]);
    }

    public function getParent()
    {
        return new SelectType(false);
    }

    public function getName()
    {
        return 'bowtype';
    }
}