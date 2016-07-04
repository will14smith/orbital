<?php

namespace AppBundle\Form\Type\Custom;

use AppBundle\Services\Enum\BadgeCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BadgeCategorySelectType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => array_flip(BadgeCategory::$choices),
        ]);
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}
