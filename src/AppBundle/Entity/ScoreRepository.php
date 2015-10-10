<?php


namespace AppBundle\Entity;


use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;

class ScoreRepository extends EntityRepository
{
    public function getPersonalBests(Person $person)
    {
        $pbs_nested = $this->createQueryBuilder('a');
        $pbs_nested_1 = $pbs_nested->expr()->andX(
            $pbs_nested->expr()->lt('s.score', 'a.score'),
            $pbs_nested->expr()->eq('s.round', 'a.round')
        );
        $pbs_nested_2 = $pbs_nested->expr()->andX(
            $pbs_nested->expr()->eq('s.score', 'a.score'),
            $pbs_nested->expr()->eq('s.round', 'a.round'),
            $pbs_nested->expr()->gt('s.date_shot', 'a.date_shot')
        );
        $pbs_nested = $pbs_nested
            ->where($pbs_nested->expr()->orX($pbs_nested_1, $pbs_nested_2))
            ->andWhere('a.person = :person');

        return $this->createQueryBuilder('s')
            ->where((new Expr())->not((new Expr())->exists($pbs_nested->getDQL())))
            ->andWhere('s.person = :person')

            ->setParameter('person', $person->getId())
            ->getQuery()
            ->getResult();
    }

    /**
     * @param Person $person
     * @param \DateTime $start_date
     * @param \DateTime $end_date
     * @return Score[]
     */
    public function getScoresByPersonBetween(Person $person, \DateTime $start_date, \DateTime $end_date){
        $score_query = $this->createQueryBuilder('s');

        return $score_query
            ->where('s.person = :person_id')
            ->andWhere('s.date_shot >= :start_date')
            ->andWhere('s.date_shot <= :end_date')

            ->setParameter('person_id', $person->getId())
            ->setParameter('start_date', $start_date, Type::DATE)
            ->setParameter('end_date', $end_date, Type::DATE)

            ->getQuery()
            ->getResult();
    }

    public function findAll()
    {
        return $this->createQueryBuilder("s");
    }
    public function findByPerson($personId)
    {
        return $this->findAll()
            ->where('s.person = :person_id')
            ->setParameter('person_id', $personId);
    }
    public function findByCompetition($value)
    {
        return $this->findAll()
            ->where('s.competition = :value')
            ->setParameter('value', $value);
    }
    public function findByApproval($value)
    {
        $q = $this->findAll();

        if(!$value) {
            return $q->where('s.date_accepted IS NULL');
        } else {
            return $q->where('s.date_accepted IS NOT NULL');
        }
    }

    /**
     * @param int $personId
     * @param bool $indoor
     * @return Score[]
     */
    public function findByPersonAndLocation($personId, $indoor)
    {
        return $this->createQueryBuilder("s")
            ->join('s.round', 'r')
            ->where('s.person = :person_id')
            ->andWhere('r.indoor = :indoor')

            ->setParameter('person_id', $personId)
            ->setParameter('indoor', $indoor)

            ->getQuery()
            ->getResult();
    }
}
