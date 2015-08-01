<?php

namespace AppBundle\Form\Type;

use AppBundle\Services\Enum\ScoreZones;
use AppBundle\Services\Enum\Unit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoundTargetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('distance_value')
            ->add('distance_unit', 'choice', ['choices' => Unit::$choices])
            ->add('target_value')
            ->add('target_unit', 'choice', ['choices' => Unit::$choices])
            ->add('scoring_zones', 'choice', ['choices' => ScoreZones::$choices])
            ->add('arrow_count')
            ->add('end_size');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\RoundTarget'
        ]);
    }

    public function getName()
    {
        return 'round_target';
    }
}
