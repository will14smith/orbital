<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\CompetitionSessionRound;
use AppBundle\Form\Type\Custom\BowTypeSelectType;
use AppBundle\Form\Type\Custom\RoundSelectType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompetitionEntryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $admin = $options['admin'];
        $rounds = $options['rounds']->map(function (CompetitionSessionRound $sessionRound) {
            return $sessionRound->getRound();
        });


        if ($admin) {
            $builder
                ->add('club')
                ->add('person');

            $archer = $builder->create('scores', 'form', ['inherit_data' => true, 'label' => false, 'attr' => ['orbital-collapse' => 'Archery Info']]);
        } else {
            $archer = $builder;
        }

        $builder->add('round', RoundSelectType::class, ['choices' => $rounds]);

        $archer->add('bowtype', BowTypeSelectType::class, ['required' => false,]);

        if ($admin) {
            $builder
                ->add($archer)
                ->add('date_entered')
                ->add('boss_number')
                ->add('target_number');
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\CompetitionSessionEntry',

            'admin' => false,
            'rounds' => [],
        ]);
    }
}
