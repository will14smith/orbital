<?php

namespace AppBundle\Form\Type;

use AppBundle\Form\Type\Custom\BowTypeSelectType;
use AppBundle\Form\Type\Custom\PersonSelectType;
use AppBundle\Form\Type\Custom\RoundSelectType;
use AppBundle\Form\Type\Custom\SkillSelectType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ScoreType extends AbstractType
{
    private $edit;

    public function __construct($edit = false)
    {
        $this->edit = $edit;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('person', new PersonSelectType(), [
                'disabled' => $this->edit
            ])
            ->add('skill', new SkillSelectType(), [
                'required' => false,
            ])
            ->add('bowtype', new BowTypeSelectType(), [
                'required' => false,
            ])
            ->add('round', new RoundSelectType(), [
                'disabled' => $this->edit
            ])
            ->add('score', 'integer', ['required' => false])
            ->add('golds', 'integer', ['required' => false])
            ->add('hits', 'integer', ['required' => false])
            ->add('competition', 'checkbox', ['required' => false, 'label' => 'Was it shot at a competition?'])
            ->add('complete', 'hidden')
            ->add('date_shot', 'date', [
                'label' => 'When was (or will be) this shot?',
                'widget' => 'single_text',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Score'
        ]);
    }

    public function getName()
    {
        return 'score';
    }
}
