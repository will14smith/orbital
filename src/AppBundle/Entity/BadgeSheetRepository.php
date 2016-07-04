<?php

namespace AppBundle\Entity;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;

class BadgeSheetRepository extends EntityRepository
{
    public function mark(BadgeSheet $sheet, $markType)
    {
        $badges = $this->createQueryBuilder('bs')
            ->where('bs.id = :sheet_id')
            ->leftJoin('bs.badgeHolders', 'bh')
            ->select('bh.id')

            ->setParameter('sheet_id', $sheet->getId())
            ->getQuery()->getArrayResult();
        $badgeIds = array_map('current', $badges);

        $q = $this->_em->createQueryBuilder()
            ->update('AppBundle\Entity\BadgeHolder', 'bh');

        if ($markType == 1) {
            $q = $q->set('bh.date_delivered', ':date');
        } else {
            $q = $q->set('bh.date_made', ':date');
        }

        $q = $q
            ->where($q->expr()->in('bh.id', $badgeIds))
            ->setParameter('date', new \DateTime(), Type::DATETIME);

        return $q->getQuery()->execute();
    }
}
