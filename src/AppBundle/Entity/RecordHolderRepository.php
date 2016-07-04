<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

class RecordHolderRepository extends EntityRepository
{
    public function getUnconfirmed()
    {
        return $this->createQueryBuilder('rh')
            ->where('rh.date_confirmed IS NULL')

            ->getQuery()->getResult();
    }

    public function getUnconfirmedByClub(Club $club)
    {
        return $this->createQueryBuilder('rh')
            ->where('rh.date_confirmed IS NULL')
            ->andWhere('rh.club = :club')

            ->setParameter('club', $club)

            ->getQuery()->getResult();
    }
}
