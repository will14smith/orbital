<?php

namespace AppBundle\Entity;

use AppBundle\Services\Enum\Skill;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;

class RecordRepository extends EntityRepository
{
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

    /**
     * @param Score $score
     *
     * @return Record[]
     */
    public function getPossibleRecordsBroken(Score $score)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $q = $qb->select('r')
            ->from('AppBundle:Record', 'r')
            ->join('r.rounds', 'rr')
            ->where('rr.round = :round')
            ->setParameter('round', $score->getRound());

        if ($score->getSkill() != Skill::NOVICE) {
            $q = $q->andWhere('rr.skill IS NULL OR rr.skill = :skill')
                ->setParameter('skill', Skill::SENIOR);
        }

        $q = $q
            ->andWhere('rr.bowtype IS NULL OR rr.bowtype = :bowtype')
            ->andWhere('rr.gender IS NULL OR rr.gender = :gender')
            ->setParameter('bowtype', $score->getBowtype())
            ->setParameter('gender', $score->getPerson()->getGender());

        return $q->getQuery()->getResult();
    }

    /**
     * @param Round $round
     *
     * @return Record[]
     */
    public function getByRound(Round $round)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $q = $qb->select('r')
            ->from('AppBundle:Record', 'r')
            ->join('r.rounds', 'rr')
            ->where('rr.round = :round')
            ->setParameter('round', $round);

        return $q->getQuery()->getResult();
    }

    public function getByClub(Club $club)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $q = $qb->select('r')
            ->from('AppBundle:Record', 'r')
            ->join('r.clubs', 'rc')
            ->where('rc.club = :club')
            ->setParameter('club', $club);

        return $q->getQuery()->getResult();
    }
}
