<?php

namespace AppBundle\Form\Type;

use AppBundle\Form\Type\Custom\BowTypeSelectType;
use AppBundle\Form\Type\Custom\ClubSelectType;
use AppBundle\Form\Type\Custom\CompetitionSelectType;
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
            ->add('club', ClubSelectType::class, [
                'required' => false,
                'disabled' => $edit
            ])
            ->add('bowtype', BowTypeSelectType::class, [
                'required' => false,
            ])
            ->add('round', RoundSelectType::class, [
                'disabled' => $edit
            ])
            ->add('competition', CompetitionSelectType::class, ['required' => false])
            ->add('score', IntegerType::class, ['required' => true])
            ->add('golds', IntegerType::class, ['required' => true])
            ->add('hits', IntegerType::class, ['required' => true])
            ->add('date_shot', DateType::class, [
                'label' => 'When was this shot?',
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
