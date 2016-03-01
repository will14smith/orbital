<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;

class ReassessType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('start_date', DateType::class, ['required' => false, 'widget' => 'single_text'])
            ->add('end_date', DateType::class, ['required' => false, 'widget' => 'single_text']);
    }
}
