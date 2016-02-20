<?php

namespace AppBundle\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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
        return $builder->create('proof', FormType::class, ['mapped' => false, 'label' => false, 'attr' => ['orbital-collapse' => 'Proof']])
            ->add('proof_images', CollectionType::class, [
                'entry_type' => FileType::class,
                'allow_add' => true,
            ])
            ->add('proof_people', CollectionType::class, [
                'entry_type' => EntityType::class,
                'entry_options' => [
                    'class' => 'AppBundle:Person',
                ],
                'allow_add' => true,
            ])
            ->add('proof_notes', TextareaType::class, ['required' => false]);
    }
}
