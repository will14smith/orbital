<?php

namespace AppBundle\Form\Type;

use AppBundle\Form\Type\Custom\BowTypeSelectType;
use AppBundle\Form\Type\Custom\GenderSelectType;
use AppBundle\Form\Type\Custom\SkillSelectType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
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
            ->add('gender', GenderSelectType::class)
            ->add('date_of_birth', BirthdayType::class, ['required' => false])
            ->add('skill', SkillSelectType::class)
            ->add('bowtype', BowTypeSelectType::class)
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
