<?php

namespace AppBundle\Form\Type;

use AppBundle\Services\Enum\BowType;
use AppBundle\Services\Enum\Gender;
use AppBundle\Services\Enum\Skill;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

class RecordMatrixType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('round', EntityType::class, ['class' => 'AppBundle:Round', 'required' => true])
            ->add('num_holders', IntegerType::class, ['required' => true])
            ->add('skill', ChoiceType::class, ['choices' => Skill::$choices, 'multiple' => true])
            ->add('gender', ChoiceType::class, ['choices' => Gender::$choices, 'multiple' => true])
            ->add('bowtype', ChoiceType::class, ['choices' => BowType::$choices, 'multiple' => true])
        ;
    }
}