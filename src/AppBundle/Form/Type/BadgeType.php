<?php

namespace AppBundle\Form\Type;

use AppBundle\Services\Enum\BadgeCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BadgeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description', 'textarea')
            ->add('algo_name')
            ->add('category', 'choice', [
                'choices' => BadgeCategory::$choices,
            ])
            ->add('multiple', 'checkbox', [
                'required' => false
            ])
            ->add('image', 'file', [
                'required' => false,
                'mapped' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Badge',
        ]);
    }

    public function getName()
    {
        return 'badge';
    }
}
