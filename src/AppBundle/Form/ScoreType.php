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

    public function __construct($edit = false)
    {
        $this->edit = $edit;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $mode = $builder->create('mode', 'form', ['inherit_data' => true, 'label' => false, 'attr' => ['orbital-collapse' => 'Skill / Bowtype']])
            ->add('skill', 'choice', [
                'choices' => Skill::$choices,
                'required' => false,
            ])
            ->add('bowtype', 'choice', [
                'choices' => BowType::$choices,
                'required' => false,
            ]);

        $score = $builder->create('score', 'form', ['inherit_data' => true, 'label' => false, 'attr' => ['class' => 'inline']])
            ->add('score')
            ->add('golds')
            ->add('hits');

        $checks = $builder->create('checks', 'form', ['inherit_data' => true, 'label' => false, 'attr' => ['class' => 'inline']])
            ->add('competition', 'checkbox', ['required' => false])
            ->add('complete', 'checkbox', ['required' => false]);

        $proof = $builder->create('proof', 'form', ['mapped' => false, 'label' => false, 'attr' => ['orbital-collapse' => 'Proof']])
            ->add('proof_images', 'collection', [
                'type' => 'file',
                'allow_add' => true,
            ])
            ->add('proof_notes', 'textarea', ['required' => false]);

        $builder
            ->add('person', 'entity', [
                'class' => 'AppBundle:Person',
                'disabled' => $this->edit
            ])
            ->add($mode)
            ->add('round', 'entity', [
                'class' => 'AppBundle:Round',
                'disabled' => $this->edit
            ])
            ->add($score)
            ->add($checks)
            ->add('date_shot')
            ->add($proof);
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