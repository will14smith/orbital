<?php

namespace AppBundle\Form\Type\Custom;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonSelectType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => 'AppBundle:Person'
        ]);
    }

    public function getParent()
    {
        return EntityType::class;
    }
}