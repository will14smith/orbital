<?php

namespace AppBundle\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LeagueMatchType extends AbstractProofType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $admin = $options['admin'];

        if ($admin) {
            $builder
                ->add('challenger');
        }

        $scores = $builder->create('scores', 'form', ['mapped' => false, 'label' => false, 'attr' => ['orbital-collapse' => 'Scores']])
            ->add('challenger_score', EntityType::class, [
                'class' => 'AppBundle:Score',
                'required' => false,
            ])
            ->add('challenger_score_value', IntegerType::class, [
                'required' => false,
            ])
            ->add('challengee_score', EntityType::class, [
                'class' => 'AppBundle:Score',
                'required' => false,
            ])
            ->add('challengee_score_value', IntegerType::class, [
                'required' => false,
            ]);

        $builder
            ->add('challengee')
            ->add('round', EntityType::class, [
                'class' => 'AppBundle:Round',
                'required' => true,
            ])
            ->add('accepted', CheckboxType::class, [
                'required' => false,
            ])
            ->add($scores)
            ->add('result', CheckboxType::class, [
                'label' => 'Challenger won?',
                'required' => false,
            ])
            ->add('date_challenged');

        $builder->add($this->getProofForm($builder));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\LeagueMatch',

            'admin' => false,
        ]);
    }
}
