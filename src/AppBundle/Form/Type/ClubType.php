<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClubType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('website')
            ->add('email')

            ->add('records_title', TextType::class, ['label' => 'Records title'])
            ->add('records_image_url', UrlType::class, ['label' => 'Records title image', 'required' => false])
            ->add('records_preface', TextareaType::class, ['label' => 'Records preface', 'required' => false])
            ->add('records_appendix', TextareaType::class, ['label' => 'Records appendix', 'required' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Club'
        ]);
    }
}
