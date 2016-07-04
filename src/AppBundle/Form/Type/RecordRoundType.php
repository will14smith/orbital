<?php

namespace AppBundle\Form\Type;

use AppBundle\Form\Type\Custom\BowTypeSelectType;
use AppBundle\Form\Type\Custom\GenderSelectType;
use AppBundle\Form\Type\Custom\SkillSelectType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecordRoundType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('round')
            ->add('count')
            ->add('skill', SkillSelectType::class, ['required' => false])
            ->add('bowtype', BowTypeSelectType::class, ['required' => false])
            ->add('gender', GenderSelectType::class, ['required' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\RecordRound',
        ]);
    }
}
