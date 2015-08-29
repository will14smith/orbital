<?php

namespace AppBundle\Form\Type\Custom;

use AppBundle\Services\Enum\Skill;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SkillSelectType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => Skill::$choices,
        ]);
    }

    public function getParent()
    {
        return new SelectType(false);
    }

    public function getName()
    {
        return 'skill';
    }
}