<?php

namespace AppBundle\Services\Handicap;

use AppBundle\Entity\Person;
use AppBundle\Entity\PersonHandicap;
use AppBundle\Entity\Score;
use AppBundle\Services\Enum\HandicapType;
use Doctrine\Bundle\DoctrineBundle\Registry;

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

    public function updateHandicap(Score $score)
    {
        $person = $score->getPerson();
        $old_hc = $person->getCurrentHandicap();

        $handicap = new PersonHandicap();

        if (!$old_hc) {
            $scores = $this->doctrine->getRepository('AppBundle:Score')
                ->findBy(
                    ['person' => $person],
                    ['date_shot' => 'ASC']
                );

            if (count($scores) >= 3) {
                $new_hc = $this->rebuild($scores);
                $handicap->setType(HandicapType::INITIAL);
            } else {
                // can't do anything...
                return;
            }
        } else {
            $score_handicap = $this->calculator->handicapForScore($score);
            $new_hc = ceil(($score_handicap + $old_hc->getHandicap()) / 2);

            $handicap->setType(HandicapType::UPDATE);
            $handicap->setScore($score);
        }

        if (!$old_hc || $new_hc < $old_hc->getHandicap()) {
            $handicap->setPerson($person);
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
     *
     * @return int
     */
    private function rebuild(array $scores)
    {
        $handicaps = array_map(function ($score) {
            return $this->calculator->handicapForScore($score);
        }, $scores);

        // initial handicap
        $handicap = ceil(($handicaps[0] + $handicaps[1] + $handicaps[2]) / 3);

        // average it up for the remainder
        for ($i = 3; $i < count($handicaps); $i++) {
            $new_hc = ceil(($handicaps[$i] + $handicap) / 2);
            if ($new_hc < $handicap) {
                $handicap = $new_hc;
            }
        }

        return $handicap;
    }

    public function reassess(Person $person, $start_date = null, $end_date = null)
    {
        if ($start_date === null) {
            $start_date = new \DateTime('1 year ago');
        }
        if ($end_date === null) {
            $end_date = new \DateTime('now');
        }

        // take all scores since $start_date
        $scores = $this->doctrine->getRepository('AppBundle:Score')
            ->getScoresByPersonBetween($person, $start_date, $end_date);

        if(count($scores) < 3) {
            return;
        }

        // compute handicaps
        $handicaps = array_map(function ($score) {
            return $this->calculator->handicapForScore($score);
        }, $scores);

        // take lowest 3 HC and average
        sort($handicaps);
        $hc_scores = array_slice($handicaps, 0, 3);
        $hc = ceil(array_sum($hc_scores) / count($hc_scores));

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
