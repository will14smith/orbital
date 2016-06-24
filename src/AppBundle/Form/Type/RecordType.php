<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('num_holders')
            ->add('rounds', CollectionType::class, [
                'entry_type' => RecordRoundType::class,
                'allow_add' => true,
                'by_reference' => false
            ])
            ->add('clubs', CollectionType::class, [
                'entry_type' => RecordClubType::class,
                'allow_add' => true,
                'by_reference' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Record'
        ]);
    }
}
