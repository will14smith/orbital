<?php

namespace AppBundle\Form\Type;

use AppBundle\Services\Enum\BowType;
use AppBundle\Services\Enum\Gender;
use AppBundle\Services\Enum\Skill;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('round')
            ->add('num_holders')
            ->add('skill', ChoiceType::class, ['choices' => Skill::$choices])
            ->add('bowtype', ChoiceType::class, [
                'choices' => BowType::$choices,
                'required' => false
            ])
            ->add('gender', ChoiceType::class, [
                'choices' => Gender::$choices,
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Record'
        ]);
    }
}
