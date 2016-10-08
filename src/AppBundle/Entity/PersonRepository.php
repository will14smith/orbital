<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class PersonRepository extends EntityRepository
{
    /**
     * @param int|null $club
     * @param string|null $nameStartsWith
     *
     * @return Query
     */
    public function getPagedByFilterQuery($club = null, $nameStartsWith = null)
    {
        $qb = $this->createQueryBuilder('p')
            ->orderBy('p.name');

        if ($club !== null) {
            $qb = $qb->andWhere('p.club = :club')
                ->setParameter('club', $club);
        }
        if ($nameStartsWith !== null) {
            // escape the like chars
            $nameStartsWith = str_replace('%', '\\%', str_replace('_', '\\_', str_replace('\\', '\\\\', $nameStartsWith)));

            $qb = $qb->andWhere($qb->expr()->like('p.name', ':name'))
                ->setParameter('name', $nameStartsWith . '%');
        }

        return $qb->getQuery();
    }
}
