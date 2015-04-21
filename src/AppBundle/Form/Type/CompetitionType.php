<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CompetitionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description', 'textarea', ['required' => false])
            ->add('info_only', 'checkbox', ['required' => false])
            ->add('location')
            ->add('date')
            ->add('boss_count')
            ->add('target_count')
            ->add('rounds');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Competition'
        ]);
    }

    public function getName()
    {
        return 'competition';
    }
}
