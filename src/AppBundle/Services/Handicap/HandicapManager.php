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

    /**
     * ASSUMPTION: $score is persisted.
     *
     * @param Score $score
     */
    public function updateHandicap(Score $score)
    {
        $person = $score->getPerson();
        $indoor = $score->isIndoor();
        $current_handicap = $person->getCurrentHandicap($indoor);

        $new_handicaps = [];

        if ($current_handicap) {
            // existing hc is older => average update
            if ($current_handicap->getDate() < $score->getDateShot()) {
                $new_handicap = $this->averageUpdate($current_handicap, $score);
                if ($new_handicap !== null) {
                    $new_handicaps = [$new_handicap];
                }
            } else {
                // existing hc is newer => rebuild from score
                $last_handicap = $person->getHandicaps()->filter(function (PersonHandicap $hc) use ($score) {
                    return $hc->getDate() <= $score->getDateShot();
                })->last();

                if(!$last_handicap) { $last_handicap = null; }

                $new_handicaps = $this->calculateRebuild($person, $indoor, $last_handicap);
                $this->removeAfter($person, $indoor, $last_handicap);
            }
        } else {
            // might be 3 scores => try rebuilding
            $new_handicaps = $this->calculateRebuild($person, $indoor);
        }

        if (count($new_handicaps) > 0) {
            $this->persist($person, $new_handicaps);
        }
    }

    /**
     * @param Person $person
     * @param bool $indoor
     */
    public function rebuildFromLastManual(Person $person, $indoor)
    {
        $handicaps = $person->getHandicaps();

        $handicap = null;
        for ($i = $handicaps->count() - 1; $i >= 0; $i--) {
            /** @var PersonHandicap $i_handicap */
            $i_handicap = $handicaps->get($i);
            // it can be null when handicaps are removed.
            if($i_handicap === null) {
                continue;
            }

            if ($i_handicap->getIndoor() === $indoor && $i_handicap->getType() === HandicapType::MANUAL) {
                $handicap = $i_handicap;
                break;
            }
        }

        $this->rebuild($person, $indoor, $handicap);
    }

    /**
     * @param Person $person
     * @param bool $indoor
     * @param PersonHandicap $from
     */
    public function rebuild(Person $person, $indoor, PersonHandicap $from = null)
    {
        $new_handicaps = $this->calculateRebuild($person, $indoor, $from);
        $this->removeAfter($person, $indoor, $from);

        if (count($new_handicaps) > 0) {
            $this->persist($person, $new_handicaps);
        }
    }

    /**
     * @param Person $person
     * @param \DateTime $start_date
     * @param \DateTime $end_date
     */
    public function reassess(Person $person, \DateTime $start_date = null, \DateTime $end_date = null)
    {
        if ($start_date === null) {
            $start_date = new \DateTime('1 year ago');
        }
        if ($end_date === null) {
            $end_date = new \DateTime('now');
        }

        $new_handicaps = $this->calculateReassesment($person, $start_date, $end_date);

        if (count($new_handicaps) > 0) {
            $this->persist($person, $new_handicaps);
        }
    }

    /**
     * @param Person $person
     * @param bool $indoor
     * @param PersonHandicap $from
     *
     * @return PersonHandicap[]
     */
    private function calculateRebuild(Person $person, $indoor, PersonHandicap $from = null)
    {
        // get all scores (in date order) since from
        $person_scores = $this->doctrine->getRepository('AppBundle:Score')
            ->getScoresByPersonBetween($person, $from === null ? new \DateTime('1990-01-01') : $from->getDate(), new \DateTime('now'));
        usort($person_scores, function (Score $a, Score $b) {
            return $a->getDateShot() < $b->getDateShot() ? -1 : 1;
        });

        $scores = [];
        foreach ($person_scores as $score) {
            if ($score->isIndoor() !== $indoor) {
                continue;
            }
            if (!$score->getDateAccepted()) {
                continue;
            }

            $scores[] = $score;
        }

        $score_index = 0;
        $score_count = count($scores);
        $handicaps = [];
        $last_handicap = $from;

        // if $from === null => initial
        if ($from === null) {
            if ($score_count < 3) {
                return [];
            }

            $initial_scores = array_slice($scores, 0, 3);
            $score_index = 3;

            $last_handicap = $this->initialHandicap($initial_scores, $indoor);
            $handicaps[] = $last_handicap;
        }

        // iterate remaining
        for (; $score_index < $score_count; $score_index++) {
            $next_handicap = $this->averageUpdate($last_handicap, $scores[$score_index]);
            if ($next_handicap === null) {
                continue;
            }

            $last_handicap = $next_handicap;
            $handicaps[] = $last_handicap;
        }

        return $handicaps;
    }


    /**
     * @param Person $person
     * @param \DateTime $start_date
     * @param \DateTime $end_date
     *
     * @return PersonHandicap[]
     */
    private function calculateReassesment(Person $person, \DateTime $start_date, \DateTime $end_date)
    {
        $scores = $this->doctrine->getRepository('AppBundle:Score')
            ->getScoresByPersonBetween($person, $start_date, $end_date);

        $indoor = [];
        $outdoor = [];

        foreach ($scores as $score) {
            if (!$score->getDateAccepted()) {
                continue;
            }

            $handicap = $this->calculator->handicapForScore($score);
            if ($score->isIndoor()) {
                $indoor[] = $handicap;
            } else {
                $outdoor[] = $handicap;
            }
        }

        $newHandicaps = [];

        if (count($indoor) >= 3) {
            sort($indoor);
            $indoor_scores = array_slice($indoor, 0, 3);
            $indoor_handicap = ceil(array_sum($indoor_scores) / 3);

            $newHandicaps[] = $this->createHandicap(HandicapType::REASSESS, true, $indoor_handicap, $end_date);
        }

        if (count($outdoor) >= 3) {
            sort($outdoor);
            $outdoor_scores = array_slice($outdoor, 0, 3);
            $outdoor_handicap = ceil(array_sum($outdoor_scores) / 3);

            $newHandicaps[] = $this->createHandicap(HandicapType::REASSESS, false, $outdoor_handicap, $end_date);
        }

        return $newHandicaps;
    }

    /**
     * @param Score[] $scores
     * @param bool $indoor
     * @return PersonHandicap
     */
    private function initialHandicap(array $scores, $indoor)
    {
        if (count($scores) !== 3) {
            throw new \InvalidArgumentException("Initial handicap should only have 3 scores");
        }

        $accum = 0;
        $date = null;

        foreach ($scores as $score) {
            $accum += $this->calculator->handicapForScore($score);
            $date = $date < $score->getDateShot() ? $score->getDateShot() : $date;

            if ($score->isIndoor() !== $indoor) {
                throw new \InvalidArgumentException("Score and handicap type don't match?");
            }
        }

        $handicap = ceil($accum / 3);
        return $this->createHandicap(HandicapType::INITIAL, $indoor, $handicap, $date);
    }

    /**
     * ASSUMPTION: $current_handicap is BEFORE $score
     *
     * @param PersonHandicap $current_handicap
     * @param Score $score
     *
     * @return PersonHandicap[]
     */
    private function averageUpdate(PersonHandicap $current_handicap, Score $score)
    {
        $score_handicap = $this->calculator->handicapForScore($score);
        $new_value = ceil(($current_handicap->getHandicap() + $score_handicap) / 2);

        if ($new_value >= $current_handicap->getHandicap()) {
            return null;
        }

        return $this->createHandicap(HandicapType::UPDATE, $score->isIndoor(), $new_value, $score->getDateShot());
    }

    /**
     * @param string $type
     * @param bool $indoor
     * @param int $value
     * @param \DateTime $date
     * @param Score $score
     *
     * @return PersonHandicap
     */
    private function createHandicap($type, $indoor, $value, $date, Score $score = null)
    {
        $handicap = new PersonHandicap();

        $handicap->setHandicap($value);
        $handicap->setType($type);
        $handicap->setIndoor($indoor);
        $handicap->setDate($date);

        if ($score !== null) {
            $handicap->setScore($score);
        }

        return $handicap;
    }

    /**
     * @param Person $person
     * @param PersonHandicap[] $handicaps
     */
    private function persist(Person $person, array $handicaps)
    {
        $em = $this->doctrine->getManager();

        foreach ($handicaps as $handicap) {
            $handicap->setPerson($person);
            $em->persist($handicap);
        }

        $em->flush();
    }

    /**
     * @param Person $person
     * @param bool $indoor
     * @param PersonHandicap $last_handicap
     */
    private function removeAfter(Person $person, $indoor, PersonHandicap $last_handicap = null)
    {
        /** @var PersonHandicap[] $handicaps */
        $handicaps = $this->doctrine->getRepository('AppBundle:PersonHandicap')
            ->findAfter($person, $last_handicap !== null ? $last_handicap->getDate() : new \DateTime('1990-01-01'));

        $em = $this->doctrine->getManager();
        foreach ($handicaps as $handicap) {
            if ($handicap->getIndoor() !== $indoor) {
                continue;
            }

            $em->remove($handicap);
        }
        $em->flush();
    }
}