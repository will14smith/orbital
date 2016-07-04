<?php

namespace AppBundle\Form\Type;

use AppBundle\Form\Type\Custom\BowTypeSelectType;
use AppBundle\Form\Type\Custom\ClubSelectType;
use AppBundle\Form\Type\Custom\GenderSelectType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('club', ClubSelectType::class, ['required' => true])
            ->add('name')
            ->add('name_preferred')
            ->add('agb_number')
            ->add('cid')
            ->add('username')
            ->add('email')
            ->add('mobile')
            ->add('gender', GenderSelectType::class)
            ->add('date_of_birth', BirthdayType::class, ['required' => false])
            ->add('date_started', DateType::class, ['widget' => 'single_text'])
            ->add('bowtype', BowTypeSelectType::class)
            ->add('club_bow');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Person',
        ]);
    }
}
