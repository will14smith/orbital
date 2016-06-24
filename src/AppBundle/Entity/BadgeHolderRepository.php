<?php

namespace AppBundle\Entity;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;

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

    public function findByIncompleteAndClub(Club $club)
    {
        return $this->createQueryBuilder('bh')
            ->join('bh.badge', 'b')
            ->where('bh.date_delivered IS NULL')
            ->andWhere('b.club = :club')
            ->setParameter('club', $club);
    }

    public function findByIdentAndPerson($ident, $person_id)
    {
        $badgeCondition = (new Expr)->orX(
            (new Expr)->eq('b.algo_name', ':ident'),
            (new Expr)->like('b.algo_name', ':ident_fuzzy')
        );

        $subquery = $this->createQueryBuilder('bh')
            ->where('bh.badge = b')
            ->andWhere('bh.person = :person');

        $holder = (new Expr)->exists($subquery);

        return $this->_em->createQueryBuilder()
            ->from('AppBundle:Badge', 'b')
            ->select('b')
            ->where($badgeCondition)
            ->andWhere($holder)

            ->setParameter('ident', $ident)
            ->setParameter('ident_fuzzy', $ident . ':%')
            ->setParameter('person', $person_id)

            ->getQuery()
            ->getResult();
    }
}
