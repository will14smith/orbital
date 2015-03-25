<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

class BadgeHolderRepository extends EntityRepository
{
    public function getByRoundup($start_date, $end_date)
    {
        $qb = $this->createQueryBuilder('b');

        $q = $qb->where('b.date_awarded >= :start_date')
            ->andWhere('b.date_awarded <= :end_date')
            ->orderBy('b.date_awarded', 'DESC')

            ->setParameter('start_date', $start_date, \Doctrine\DBAL\Types\Type::DATETIME)
            ->setParameter('end_date', $end_date, \Doctrine\DBAL\Types\Type::DATETIME)
        ;

        return $q->getQuery()->getResult();
    }
}
