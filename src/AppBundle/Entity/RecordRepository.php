<?php

namespace AppBundle\Entity;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;

class RecordRepository extends EntityRepository
{
    public function award(Record $record, RecordHolder $new_holder)
    {
        $em = $this->getEntityManager();

        $current_holder = $record->getCurrentHolder();
        if ($current_holder) {
            $current_holder->setDateBroken($new_holder->getDate());
        }

        $new_holder->setRecord($record);

        $total = 0;
        foreach ($new_holder->getPeople() as $person) {
            $total += $person->getScoreValue();

            $person->setRecordHolder($new_holder);
            $em->persist($person);
        }
        $new_holder->setScore($total);

        $em->persist($new_holder);
        $em->flush();
    }

    /**
     * @param int $person_id
     *
     * @return RecordHolderPerson[]
     */
    public function findByPerson($person_id)
    {

        $people = [];
        for ($i = 0; $i < 2; $i++) {
            $qb = $this->getEntityManager()->createQueryBuilder();

            $q = $qb->select('rhp', 'rh', 'r')
                ->from('AppBundle:RecordHolderPerson', 'rhp')
                ->join('rhp.record_holder', 'rh')
                ->join('rh.record', 'r')
                ->where('rhp.person = :person')
                ->addOrderBy('rh.date', 'DESC')
                ->setParameter('person', $person_id);

            if ($i == 0) {
                $q = $q->andWhere('rh.date_broken IS NULL');
            } else {
                $q = $q->andWhere('rh.date_broken IS NOT NULL');
            }

            $people = array_merge($people, $q->getQuery()->getResult());
        }

        return $people;
    }

    /**
     * @param \DateTime $start_date
     * @param \DateTime $end_date
     *
     * @return array
     */
    public function getByRoundup($start_date, $end_date)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $q = $qb->select('rh', 'rhp', 'r')
            ->from('AppBundle:RecordHolder', 'rh')
            ->join('rh.people', 'rhp')
            ->join('rh.record', 'r')
            ->where('rh.date >= :start_date')
            ->andWhere('rh.date <= :end_date')
            ->orderBy('rh.date', 'DESC')
            ->setParameter('start_date', $start_date, Type::DATETIME)
            ->setParameter('end_date', $end_date, Type::DATETIME);

        return $q->getQuery()->getResult();
    }
}
