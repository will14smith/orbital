<?php

namespace AppBundle\Entity;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;

class PersonHandicapRepository extends EntityRepository
{
    public function exists(Score $score)
    {
        $q = $this->createQueryBuilder('ph')
            ->select('count(ph.id)')
            ->where('ph.score = :score')
            ->setParameter('score', $score->getId());

        return $q->getQuery()->getSingleScalarResult();
    }

    public function findAfter(Person $person, \DateTime $date)
    {
        return $this->createQueryBuilder('ph')
            ->where('ph.person = :person')
            ->andWhere('ph.date > :date')

            ->setParameter('person', $person->getId())
            ->setParameter('date', $date, Type::DATE)
            ->getQuery()
            ->getResult();
    }
}
