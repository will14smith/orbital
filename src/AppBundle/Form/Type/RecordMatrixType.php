<?php

namespace AppBundle\Form\Type;

use AppBundle\Form\Type\Custom\BowTypeSelectType;
use AppBundle\Form\Type\Custom\GenderSelectType;
use AppBundle\Form\Type\Custom\RoundSelectType;
use AppBundle\Form\Type\Custom\SkillSelectType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

class RecordMatrixType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('round', RoundSelectType::class, ['required' => true])
            ->add('num_holders', IntegerType::class, ['required' => true])
            ->add('skill', SkillSelectType::class, ['multiple' => true])
            ->add('gender', GenderSelectType::class, ['multiple' => true])
            ->add('bowtype', BowTypeSelectType::class, ['multiple' => true])
        ;
    }
}
