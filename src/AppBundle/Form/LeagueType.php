<?php


namespace AppBundle\Form;

use AppBundle\Services\Enum\BowType;
use AppBundle\Services\Enum\Gender;
use AppBundle\Services\Enum\Skill;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LeagueType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description', 'textarea', [
                'required' => false
            ])
            ->add('algo_name')
            #region LIMIT
            ->add('open_date')
            ->add('close_date')
            ->add('skill_limit', 'choice', [
                'choices' => Skill::$choices,
                'required' => false
            ])
            ->add('bowtype_limit', 'choice', [
                'choices' => Bowtype::$choices,
                'required' => false
            ])
            ->add('gender_limit', 'choice', [
                'choices' => Gender::$choices,
                'required' => false
            ])
            #end region
            ->add('rounds', 'entity', [
                'class' => 'AppBundle:Round',
                'multiple' => true,
                'required' => false
            ])
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\League'
        ]);
    }

    public function getName()
    {
        return 'league';
    }
}