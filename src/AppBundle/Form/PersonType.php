<?php


namespace AppBundle\Form;

use AppBundle\Services\Enum\BowType;
use AppBundle\Services\Enum\Gender;
use AppBundle\Services\Enum\Skill;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

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
            ->add('gender', 'choice', ['choices' => Gender::$choices])
            ->add('date_of_birth', 'birthday')
            ->add('skill', 'choice', ['choices' => Skill::$choices])
            ->add('bowtype', 'choice', ['choices' => BowType::$choices])
            ->add('club_bow')
            ->add('password', 'password', ['required' => false])
            ->add('admin', 'checkbox', ['required' => false])
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Person'
        ));
    }

    public function getName()
    {
        return 'person';
    }
}