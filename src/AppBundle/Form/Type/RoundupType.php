<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;

class RoundupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('start_date', DateType::class, ['widget' => 'single_text'])
            ->add('end_date', DateType::class, ['widget' => 'single_text'])
            ->add('type', ChoiceType::class, [
                'multiple' => true,
                'required' => true,
                'choices' => [
                    'records' => 'Records',
                    'badges' => 'Badges',
                    'leagues' => 'Leagues',
                    'competitions' => 'Competitions',
                ],
            ]);
    }
}
