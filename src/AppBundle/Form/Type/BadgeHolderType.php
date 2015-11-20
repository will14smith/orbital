<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
            ->add('badge', null, ['required' => true])
            ->add('person', null, ['required' => true])
            ->add('date_awarded', 'date', ['label' => 'Date claimed', 'widget' => 'single_text']);

        if ($this->admin) {
            $builder
                ->add('date_confirmed', 'date', ['widget' => 'single_text'])
                ->add('date_made', 'date', ['widget' => 'single_text', 'required' => false])
                ->add('date_delivered', 'date', ['widget' => 'single_text', 'required' => false]);
        }

        if ($this->show_proof) {
            $builder->add($this->getProofForm($builder));
        }
    }

    public function configureOptions(OptionsResolver $resolver)
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
