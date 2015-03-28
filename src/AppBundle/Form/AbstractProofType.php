<?php
/**
 * Created by PhpStorm.
 * User: will_000
 * Date: 28/03/2015
 * Time: 00:38
 */

namespace AppBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

abstract class AbstractProofType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     *
     * @return FormBuilderInterface
     */
    protected function getProofForm(FormBuilderInterface $builder)
    {
        return $builder->create('proof', 'form', ['mapped' => false, 'label' => false, 'attr' => ['orbital-collapse' => 'Proof']])
            ->add('proof_images', 'collection', [
                'type' => 'file',
                'allow_add' => true,
            ])
            ->add('proof_people', 'collection', [
                'type' => 'entity',
                'options' => [
                    'class' => 'AppBundle:Person',
                ],
                'allow_add' => true,
            ])
            ->add('proof_notes', 'textarea', ['required' => false]);
    }
}