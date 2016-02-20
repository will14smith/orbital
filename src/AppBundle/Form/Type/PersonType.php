<?php

namespace AppBundle\Form\Type;

use AppBundle\Services\Enum\BowType;
use AppBundle\Services\Enum\Gender;
use AppBundle\Services\Enum\Skill;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('club')
            ->add('name')
            ->add('name_preferred')
            ->add('agb_number')
            ->add('cid')
            ->add('cuser')
            ->add('email')
            ->add('mobile')
            ->add('gender', ChoiceType::class, ['choices' => Gender::$choices])
            ->add('date_of_birth', BirthdayType::class, ['required' => false])
            ->add('skill', ChoiceType::class, ['choices' => Skill::$choices])
            ->add('bowtype', ChoiceType::class, ['choices' => BowType::$choices])
            ->add('club_bow')
            ->add('password', PasswordType::class, ['required' => false])
            ->add('admin', CheckboxType::class, ['required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Person'
        ]);
    }
}
