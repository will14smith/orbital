<?php

namespace AppBundle\Entity;

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
}
