<?php

namespace AppBundle\Form\Type\Custom;

use AppBundle\Constants;
use AppBundle\Entity\Competition;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompetitionSelectType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => 'AppBundle:Competition',
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('c')
                    ->orderBy('c.date', 'DESC');
            },
            'choice_label' => function (Competition $competition) {
                return $competition->getName() . ' - ' . $competition->getDate()->format(Constants::DATE_FORMAT);
            }
        ]);
    }

    public function getParent()
    {
        return EntityType::class;
    }
}