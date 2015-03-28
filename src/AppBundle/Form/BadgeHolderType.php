<?php


namespace AppBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BadgeHolderType extends AbstractProofType
{
    /**
     * @var bool
     */
    private $admin;
    /**
     * @var bool
     */
    private $show_proof;

    /**
     * @param bool $admin
     * @param bool $show_proof
     */
    public function __construct($admin, $show_proof = true)
    {
        $this->admin = $admin;
        $this->show_proof = $show_proof;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('badge')
            ->add('person')
            ->add('date_awarded', 'date', ['label' => 'Date Claimed']);

        if ($this->admin) {
            $builder
                ->add('date_confirmed')
                ->add('date_made')
                ->add('date_delivered');
        }

        if ($this->show_proof) {
            $builder->add($this->getProofForm($builder));
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\BadgeHolder'
        ]);
    }

    public function getName()
    {
        return 'badge_holder';
    }
}