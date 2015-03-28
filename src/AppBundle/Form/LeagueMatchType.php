<?php


namespace AppBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LeagueMatchType extends AbstractProofType
{
    /**
     * @var bool
     */
    private $admin;

    /**
     * @param bool $admin
     */
    public function __construct($admin)
    {
        $this->admin = $admin;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($this->admin) {
            $builder
                ->add('challenger');
        }

        $scores = $builder->create('scores', 'form', ['mapped' => false, 'label' => false, 'attr' => ['orbital-collapse' => 'Scores']])
            ->add('challenger_score', 'entity', [
                'class' => 'AppBundle:Score',
                'required' => false
            ])
            ->add('challenger_score_value', 'integer', [
                'required' => false
            ])
            ->add('challengee_score', 'entity', [
                'class' => 'AppBundle:Score',
                'required' => false
            ])
            ->add('challengee_score_value', 'integer', [
                'required' => false
            ]);

        $builder
            ->add('challengee')
            ->add('round', 'entity', [
                'class' => 'AppBundle:Round',
                'required' => true
            ])
            ->add('accepted', 'checkbox', [
                'required' => false
            ])
            ->add($scores)
            ->add('result', 'checkbox', [
                'label' => 'Challenger won?',
                'required' => false
            ])
            ->add('date_challenged');

        $builder->add($this->getProofForm($builder));

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\LeagueMatch'
        ]);
    }

    public function getName()
    {
        return 'league_match';
    }
}