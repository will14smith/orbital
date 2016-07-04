<?php

namespace AppBundle\Form\Type;

use AppBundle\Form\Type\Custom\ScoreZoneSelectType;
use AppBundle\Form\Type\Custom\UnitSelectType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoundTargetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('distance_value')
            ->add('distance_unit', UnitSelectType::class)
            ->add('target_value')
            ->add('target_unit', UnitSelectType::class)
            ->add('scoring_zones', ScoreZoneSelectType::class)
            ->add('arrow_count')
            ->add('end_size');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\RoundTarget',
        ]);
    }
}
