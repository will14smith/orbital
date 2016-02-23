<?php

namespace AppBundle\Form\Type;

use AppBundle\Form\Type\Custom\BowTypeSelectType;
use AppBundle\Form\Type\Custom\GenderSelectType;
use AppBundle\Form\Type\Custom\RoundSelectType;
use AppBundle\Form\Type\Custom\SkillSelectType;
use AppBundle\Services\Leagues\LeagueManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LeagueType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var LeagueManager $leagueManager */
        $leagueManager = $options['manager'];

        $builder
            ->add('name')
            ->add('description', TextareaType::class, [
                'required' => false
            ])
            ->add('algo_name', ChoiceType::class, [
                'choices' => array_flip($leagueManager->getAlgorithmNames()),
                'required' => false
            ])
            #region LIMIT
            ->add('open_date')
            ->add('close_date')
            ->add('skill_limit', SkillSelectType::class, ['required' => false])
            ->add('bowtype_limit', BowTypeSelectType::class, ['required' => false])
            ->add('gender_limit', GenderSelectType::class, ['required' => false])
            #end region
            ->add('rounds', RoundSelectType::class, ['multiple' => true, 'required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\League',

            'manager' => null
        ]);
    }
}
