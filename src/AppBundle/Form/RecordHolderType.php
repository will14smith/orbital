<?php


namespace AppBundle\Form;

use AppBundle\Services\Enum\BowType;
use AppBundle\Services\Enum\Gender;
use AppBundle\Services\Enum\Skill;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RecordHolderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('holders', 'collection', [
                'type' => new RecordHolderInnerType()
            ]);
    }

    public function getName()
    {
        return 'record_holder';
    }
}

class RecordHolderInnerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('person')
            ->add('score_value')
            ->add('score')
            ->add('location')
            ->add('date')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\RecordHolder'
        ));
    }

    public function getName()
    {
        return 'record_holder_inner';
    }
}