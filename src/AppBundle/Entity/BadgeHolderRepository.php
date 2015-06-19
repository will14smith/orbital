<?php

namespace AppBundle\Entity;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;

class BadgeHolderRepository extends EntityRepository
{
    public function getByRoundup($start_date, $end_date)
    {
        $qb = $this->createQueryBuilder('b');

        $q = $qb->where('b.date_awarded >= :start_date')
            ->andWhere('b.date_awarded <= :end_date')
            ->orderBy('b.date_awarded', 'DESC')
            ->setParameter('start_date', $start_date, Type::DATETIME)
            ->setParameter('end_date', $end_date, Type::DATETIME);

        return $q->getQuery()->getResult();
    }

    public function findByIncomplete()
    {
        return $this->createQueryBuilder('b')
            ->where('b.date_delivered IS NULL');
    }
}
