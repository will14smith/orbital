<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BadgeHolderType extends AbstractProofType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $admin = $options['admin'];
        $show_proof = $options['show_proof'];

        $builder
            ->add('badge', null, ['required' => true])
            ->add('person', null, ['required' => true])
            ->add('date_awarded', DateType::class, ['label' => 'Date claimed', 'widget' => 'single_text']);

        if ($admin) {
            $builder
                ->add('date_confirmed', DateType::class, ['widget' => 'single_text'])
                ->add('date_made', DateType::class, ['widget' => 'single_text', 'required' => false])
                ->add('date_delivered', DateType::class, ['widget' => 'single_text', 'required' => false]);
        }

        if ($show_proof) {
            $builder->add($this->getProofForm($builder));
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\BadgeHolder',

            'admin' => false,
            'show_proof' => true,
        ]);
    }
}
