<?php


namespace AppBundle\Form;

use AppBundle\Entity\Round;
use AppBundle\Services\Enum\BowType;
use AppBundle\Services\Enum\Gender;
use AppBundle\Services\Enum\Skill;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

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
     * @param Round[] $rounds
     */
    public function __construct($admin, $rounds)
    {
        $this->admin = $admin;
        $this->rounds = $rounds;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($this->admin) {
            $builder
                ->add('club')
                ->add('person');

            $archer = $builder->create('scores', 'form', ['mapped' => false, 'label' => false, 'attr' => ['orbital-collapse' => 'Archery Info']]);
        } else {
            $archer = $builder;
        }

        $builder->add('round', 'entity', [
            'class' => 'AppBundle:Round',
            'choices' => $this->rounds
        ]);

        $archer
            ->add('skill', 'choice', [
                'choices' => Skill::$choices,
                'required' => false,
            ])
            ->add('bowtype', 'choice', [
                'choices' => BowType::$choices,
                'required' => false,
            ])
            ->add('gender', 'choice', [
                'choices' => Gender::$choices,
                'required' => false,
            ]);

        if ($this->admin) {
            $builder
                ->add($archer)
                ->add('boss_number')
                ->add('target_number');
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\CompetitionEntry'
        ]);
    }

    public function getName()
    {
        return 'competition';
    }
}