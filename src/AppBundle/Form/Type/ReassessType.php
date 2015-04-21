<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ReassessType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('start_date', 'date', ['required' => false])
            ->add('end_date', 'date', ['required' => false]);
    }

    public function getName()
    {
        return 'reassess';
    }
}