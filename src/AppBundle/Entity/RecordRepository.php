<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

class RecordRepository extends EntityRepository
{
    public function award(Record $record, RecordHolder $new_holder)
    {
        $em = $this->getEntityManager();

        $current_holder = $record->getCurrentHolder();
        if ($current_holder) {
            //TODO check the new holder breaks the record

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
                ->setParameter('person', $person_id)
            ;

            if ($i == 0) {
                $q = $q->andWhere('rh.date_broken IS NULL');
            } else {
                $q = $q->andWhere('rh.date_broken IS NOT NULL');
            }

            $people = array_merge($people, $q->getQuery()->getResult());
        }

        return $people;
    }
}
