<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\CompetitionSessionRound;
use AppBundle\Entity\Round;
use AppBundle\Services\Enum\BowType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompetitionEntryType extends AbstractType
{
    /**
     * @var bool
     */
    private $admin;
    /**
     * @var Round[]
     */
    private $rounds;

    /**
     * @param bool $admin
     * @param \Doctrine\Common\Collections\Collection $rounds
     */
    public function __construct($admin, $rounds)
    {
        $this->admin = $admin;
        $this->rounds = $rounds->map(function(CompetitionSessionRound $sessionRound) {
            return $sessionRound->getRound();
        });
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($this->admin) {
            $builder
                ->add('club')
                ->add('person');

            $archer = $builder->create('scores', 'form', ['inherit_data' => true, 'label' => false, 'attr' => ['orbital-collapse' => 'Archery Info']]);
        } else {
            $archer = $builder;
        }

        $builder->add('round', 'entity', [
            'class' => 'AppBundle:Round',
            'choices' => $this->rounds,
        ]);

        $archer
            ->add('bowtype', 'choice', [
                'choices' => BowType::$choices,
                'required' => false,
            ]);

        if ($this->admin) {
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
            'data_class' => 'AppBundle\Entity\CompetitionSessionEntry'
        ]);
    }

    public function getName()
    {
        return 'competition_entry';
    }
}
