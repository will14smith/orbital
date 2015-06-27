<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

class CompetitionSessionEntryRepository extends EntityRepository
{
    public function findByCompetition(Competition $competition)
    {
        //TODO need to handle entries in mutliple sessions?
        $qb = $this->createQueryBuilder('e')
            ->join('e.session', 's')
            ->where('s.competition = :competition')

            ->setParameter('competition', $competition->getId());

        return $qb;
    }

    public function findBySession(CompetitionSession $session)
    {
        $qb = $this->createQueryBuilder('e')
            ->where('e.session = :session')

            ->setParameter('session', $session->getId());

        return $qb;
    }
}
