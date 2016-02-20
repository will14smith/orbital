<?php

namespace AppBundle\Form\Type;

use AppBundle\Form\Type\Custom\BowTypeSelectType;
use AppBundle\Form\Type\Custom\PersonSelectType;
use AppBundle\Form\Type\Custom\RoundSelectType;
use AppBundle\Form\Type\Custom\SkillSelectType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ScoreType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $edit = $options['editing'];

        $builder
            ->add('person', PersonSelectType::class, [
                'disabled' => $edit
            ])
            ->add('skill', SkillSelectType::class, [
                'required' => false,
            ])
            ->add('bowtype', BowTypeSelectType::class, [
                'required' => false,
            ])
            ->add('round', RoundSelectType::class, [
                'disabled' => $edit
            ])
            ->add('score', IntegerType::class, ['required' => true])
            ->add('golds', IntegerType::class, ['required' => true])
            ->add('hits', IntegerType::class, ['required' => true])
            ->add('competition', CheckboxType::class, ['required' => false, 'label' => 'Was it shot at a competition?'])
            ->add('date_shot', DateType::class, [
                'label' => 'When was (or will be) this shot?',
                'widget' => 'single_text',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Score',

            'editing' => false
        ]);
    }
}
