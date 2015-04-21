<?php

namespace AppBundle\Form\Type;

use AppBundle\Services\Enum\BowType;
use AppBundle\Services\Enum\Gender;
use AppBundle\Services\Enum\Skill;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RecordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('round')
            ->add('num_holders')
            ->add('skill', 'choice', ['choices' => Skill::$choices])
            ->add('bowtype', 'choice', [
                'choices' => Bowtype::$choices,
                'required' => false
            ])
            ->add('gender', 'choice', [
                'choices' => Gender::$choices,
                'required' => false
            ]);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Record'
        ]);
    }

    public function getName()
    {
        return 'club';
    }
}
