<?php


namespace AppBundle\Services\Handicap;


use AppBundle\Entity\Person;
use AppBundle\Entity\PersonHandicap;
use AppBundle\Entity\Score;
use AppBundle\Services\Enum\HandicapType;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;

class HandicapManager
{
    /**
     * @var Registry
     */
    private $doctrine;
    /**
     * @var HandicapCalculator
     */
    private $calculator;

    public function __construct(Registry $doctrine, HandicapCalculator $calculator)
    {
        $this->doctrine = $doctrine;
        $this->calculator = $calculator;
    }

    public function handleNewScore(Score $score)
    {
        //TODO handle out of order dates...

        $person = $score->getPerson();
        $old_hc = $person->getCurrentHandicap();
        $type = HandicapType::UPDATE;

        if (!$old_hc) {
            $scores = $this->doctrine->getRepository('AppBundle:Score')
                ->findBy(
                    ['person' => $person],
                    ['date_shot' => 'ASC']
                );

            if (count($scores) >= 3) {
                $new_hc = $this->rebuild($scores);
                $type = HandicapType::INITIAL;
            } else {
                // can't do anything...
                return;
            }
        } else {
            $score_handicap = $this->calculator->handicapForScore($score);
            $new_hc = ceil(($score_handicap + $old_hc) / 2);
        }

        if ($new_hc < $old_hc || !$old_hc) {
            $handicap = new PersonHandicap();

            $handicap->setPerson($person);
            $handicap->setType($type);
            $handicap->setDate(new \DateTime('now'));
            $handicap->setHandicap($new_hc);

            $em = $this->doctrine->getManager();
            $em->persist($handicap);
            $em->flush();
        }
    }

    /**
     * Only run this when >3 scores exist and there isn't a current handicap
     *
     * @param Score[] $scores
     * @return int
     */
    private function rebuild(array $scores)
    {
        $handicaps = array_map(function ($score) {
            return $this->calculator->handicapForScore($score);
        }, $scores);

        // initial handicap
        $handicap = ($handicaps[0] + $handicaps[1] + $handicaps[2]) / 3;

        // average it up for the remainder
        for ($i = 3; $i < count($handicaps); $i++) {
            $new_hc = ceil(($handicaps[$i] + $handicap) / 2);
            if($new_hc < $handicap) {
                $handicap = $new_hc;
            }
        }

        return $handicap;
    }

    public function reassess(Person $person, $start_date = null, $end_date = null)
    {
        if ($start_date == null) {
            $start_date = new \DateTime('1 year ago');
        }
        if ($end_date == null) {
            $end_date = new \DateTime('now');
        }

        // take all scores since $start_date
        /** @var QueryBuilder $score_query */
        $score_query = $this->doctrine->getRepository('AppBundle:Score')
            ->createQueryBuilder('s');
        $score_criteria = Criteria::create()
            ->where(Criteria::expr()->gte('date_shot', $start_date))
            ->andWhere(Criteria::expr()->lte('date_shot', $end_date));

        /** @var Score[] $scores */
        $scores = $score_query
            ->addSelect('s')
            ->addCriteria($score_criteria)
            ->getQuery()
            ->getArrayResult();

        // compute handicaps
        $handicaps = array_map(function ($score) {
            return $this->calculator->handicapForScore($score);
        }, $scores);

        // take highest 3 HC and average
        rsort($handicaps);
        $hc_scores = array_slice($handicaps, 0, 3);
        $hc = array_sum($hc_scores) / count($hc_scores);

        $handicap = new PersonHandicap();

        $handicap->setPerson($person);
        $handicap->setType(HandicapType::REASSESS);
        $handicap->setDate(new \DateTime('now'));
        $handicap->setHandicap($hc);

        $em = $this->doctrine->getManager();
        $em->persist($handicap);
        $em->flush();
    }
}