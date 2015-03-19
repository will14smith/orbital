<?php


namespace AppBundle\Form;

use AppBundle\Services\Enum\BowType;
use AppBundle\Services\Enum\Skill;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ScoreType extends AbstractType
{
    private $edit;

    public function __construct($edit = false) {
        $this->edit = $edit;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('person', 'entity', [
                'class' => 'AppBundle:Person',
                'disabled' => $this->edit
            ])
            ->add('skill', 'choice', [
                'choices' => Skill::$choices,
                'required' => false,
            ])
            ->add('bowtype', 'choice', [
                'choices' => BowType::$choices,
                'required' => false,
            ])
            ->add('round', 'entity', [
                'class' => 'AppBundle:Round',
                'disabled' => $this->edit
            ])
            //TODO group these
            ->add('score')
            ->add('golds')
            ->add('hits')
            // END GROUP
            ->add('competition', 'checkbox', ['required' => false])
            ->add('complete', 'checkbox', ['required' => false])
            ->add('date_shot');
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Score'
        ));
    }

    public function getName()
    {
        return 'club';
    }
}