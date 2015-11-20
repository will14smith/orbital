<?php

namespace AppBundle\Form\Type;

use AppBundle\Services\Enum\BowType;
use AppBundle\Services\Enum\Gender;
use AppBundle\Services\Enum\Skill;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RecordMatrixType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('round', 'entity', ['class' => 'AppBundle:Round', 'required' => true])
            ->add('num_holders', 'integer', ['required' => true])
            ->add('skill', 'choice', ['choices' => Skill::$choices, 'multiple' => true])
            ->add('gender', 'choice', ['choices' => Gender::$choices, 'multiple' => true])
            ->add('bowtype', 'choice', ['choices' => BowType::$choices, 'multiple' => true])
        ;
    }

    public function getName()
    {
        return 'record_matrix';
    }
}