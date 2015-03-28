<?php


namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RoundupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('start_date', 'date')
            ->add('end_date', 'date')
            ->add('type', 'choice', [
                'multiple' => true,
                'required' => true,
                'choices' => [
                    'records' => 'Records',
                    'badges' => 'Badges',
                    'leagues' => 'Leagues',
                    'competitions' => 'Competitions'
                ]
            ]);
    }

    public function getName()
    {
        return 'roundup';
    }
}