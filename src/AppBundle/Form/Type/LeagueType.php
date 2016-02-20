<?php

namespace AppBundle\Form\Type;

use AppBundle\Services\Enum\BowType;
use AppBundle\Services\Enum\Gender;
use AppBundle\Services\Enum\Skill;
use AppBundle\Services\Leagues\LeagueManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
                'choices' => $leagueManager->getAlgorithmNames(),
                'required' => false
            ])
            #region LIMIT
            ->add('open_date')
            ->add('close_date')
            ->add('skill_limit', ChoiceType::class, [
                'choices' => Skill::$choices,
                'required' => false
            ])
            ->add('bowtype_limit', ChoiceType::class, [
                'choices' => BowType::$choices,
                'required' => false
            ])
            ->add('gender_limit', ChoiceType::class, [
                'choices' => Gender::$choices,
                'required' => false
            ])
            #end region
            ->add('rounds', EntityType::class, [
                'class' => 'AppBundle:Round',
                'multiple' => true,
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\League',

            'manager' => null
        ]);
    }
}
