<?php

namespace AppBundle\Form\Type;

use AppBundle\Form\Type\Custom\ClubSelectType;
use AppBundle\Form\Type\Custom\CompetitionSelectType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecordHolderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', DateType::class, ['widget' => 'single_text'])
            ->add('competition', CompetitionSelectType::class)
            ->add('club', ClubSelectType::class)
            ->add('people', CollectionType::class, [
                'entry_type' => RecordHolderPersonType::class,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\RecordHolder',
        ]);
    }
}
