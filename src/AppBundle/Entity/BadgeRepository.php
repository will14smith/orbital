<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

class BadgeRepository extends EntityRepository
{
    /**
     * @param string $ident
     *
     * @return Badge[]
     */
    public function findByAlgorithm($ident)
    {
        return $this->createQueryBuilder('b')
            ->where('b.algo_name = :ident')
            ->orWhere('b.algo_name LIKE :ident_fuzzy')

            ->setParameter('ident', $ident)
            ->setParameter('ident_fuzzy', $ident . ':%')

            ->getQuery()
            ->getResult();
    }

    public function getByClub(Club $club)
    {
        $q = $this->createQueryBuilder('b')
            ->where('b.club = :club')
            ->setParameter('club', $club);

        return $q->getQuery()->getResult();
    }
}
