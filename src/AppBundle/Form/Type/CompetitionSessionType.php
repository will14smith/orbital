<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CompetitionSessionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startTime')
            ->add('bossCount')
            ->add('targetCount')
            ->add('detailCount')
            ->add('rounds', 'collection', [
                'type' => new CompetitionSessionRoundType(),
                'allow_add' => true,
                'by_reference' => false,
            ]);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\CompetitionSession'
        ]);
    }

    public function getName()
    {
        return 'competition_session';
    }
}
