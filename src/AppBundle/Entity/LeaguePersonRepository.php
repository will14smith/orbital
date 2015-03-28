<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

class LeaguePersonRepository extends EntityRepository
{
    /**
     * @param League $league
     *
     * @return int
     */
    public function getInitialPosition(League $league)
    {
        $q = $this->createQueryBuilder('lp')
            ->select('MAX(lp.initial_position)')
            ->where('lp.league = :league')

            ->setParameter('league', $league->getId())
            ;

        return $q->getQuery()->getSingleScalarResult() + 1 ?: 1;
    }
}
