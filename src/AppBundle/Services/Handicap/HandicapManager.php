<?php

namespace AppBundle\Services\Handicap;

use AppBundle\Entity\Person;
use AppBundle\Entity\PersonHandicap;
use AppBundle\Entity\Score;
use AppBundle\Services\Enum\BowType;
use AppBundle\Services\Enum\Environment;
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
        $personHandicapRepository = $this->doctrine->getRepository('AppBundle:PersonHandicap');

        $id = new HandicapIdentifier($score->getPerson(), $score->isIndoor(), $score->getBowtype());
        $current_handicap = $personHandicapRepository->findCurrent($id);

        $new_handicaps = [];

        if ($current_handicap) {
            if ($current_handicap->getDate() < $score->getDateShot()) {
                // existing hc is older => average update
                $new_handicap = $this->averageUpdate($current_handicap, $score);
                if ($new_handicap !== null) {
                    $new_handicaps = [$new_handicap];
                }
            } else {
                // existing hc is newer => rebuild
                $new_handicaps = $this->calculateRebuild($id);
                $this->remove($id);
            }
        } else {
            // might be 3 scores => try rebuilding
            $new_handicaps = $this->calculateRebuild($id);
        }

        if (count($new_handicaps) > 0) {
            $this->persist($new_handicaps);
        }
    }

    // person methods
    public function rebuildPerson(Person $person)
    {
        $environments = [Environment::INDOOR, Environment::OUTDOOR];
        $bowTypes = array_keys(BowType::$choices);

        foreach ($environments as $environment) {
            foreach ($bowTypes as $bowType) {
                $this->rebuild(new HandicapIdentifier($person, $environment, $bowType));
            }
        }
    }

    // handicap methods
    public function rebuild(HandicapIdentifier $id)
    {
        $newHandicaps = $this->calculateRebuild($id);
        $this->remove($id);
        $this->persist($newHandicaps);

        return $newHandicaps;
    }


    // functional methods

    /**
     * @param HandicapIdentifier $id
     *
     * @return \AppBundle\Entity\PersonHandicap[]
     */
    private function calculateRebuild(HandicapIdentifier $id)
    {
        // get all scores (in date order) since from
        $scores = $this->doctrine->getRepository('AppBundle:Score')
            ->getScoresByHandicapIdBetween($id, new \DateTime('1950-01-01'), new \DateTime('now'));

        $seasons = [];

        foreach ($scores as $score) {
            $season = $this->getSeason($id, $score->getDateShot())->format('Y-m-d');

            if (!array_key_exists($season, $seasons)) {
                $seasons[$season] = [];
            }

            $seasons[$season][] = $score;
        }

        $handicaps = [];
        /** @var PersonHandicap $previous_handicap */
        $previous_handicap = null;

        foreach ($seasons as $dateString => $scores) {
            $score_index = 0;
            $score_count = count($scores);

            $season_start = new \DateTime($dateString);
            $season_end = clone $season_start;
            $season_end->add(new \DateInterval('P1Y'));

            // ignore prev if it wasn't last season
            if ($previous_handicap !== null) {
                $diff = $season_start->diff($previous_handicap->getDate());

                if ($diff->y > 0) {
                    $previous_handicap = null;
                }
            }

            // if $previous_handicap === null => initial
            if ($previous_handicap === null) {
                if ($score_count < 3) {
                    // nothing in this season
                    continue;
                }

                $initial_scores = array_slice($scores, 0, 3);
                $score_index = 3;

                $previous_handicap = $this->initialHandicap($id, $initial_scores);
                $handicaps[] = $previous_handicap;
            }


            // iterate remaining
            for (; $score_index < $score_count; ++$score_index) {
                $new_handicap = $this->averageUpdate($previous_handicap, $scores[$score_index]);
                if ($new_handicap === null) {
                    // this score doesn't change anything
                    continue;
                }

                $handicaps[] = $new_handicap;
                $previous_handicap = $new_handicap;
            }

            // if the season has ended do the annual re-assessment
            if ($season_end <= new \DateTime('now')) {
                $handicaps = array_merge($handicaps, $this->calculateAssessment($id, $season_start, $season_end));
            }
        }

        return $handicaps;
    }

    private function getSeason(HandicapIdentifier $id, \DateTime $date)
    {
        $threshold_date = new \DateTime();
        $threshold_date->setTime(0, 0, 0);

        // ArcheryGB dates
        if ($id->isIndoor()) {
            $threshold_date->setDate($date->format('Y'), 7, 1);
            if ($threshold_date > $date) {
                $threshold_date->sub(new \DateInterval('P1Y'));
            }
        } else {
            $threshold_date->setDate($date->format('Y'), 1, 1);
        }

        return $threshold_date;
    }

    /**
     * @param HandicapIdentifier $id
     * @param \DateTime          $start_date
     * @param \DateTime          $end_date
     *
     * @return \AppBundle\Entity\PersonHandicap[]
     */
    private function calculateAssessment(HandicapIdentifier $id, \DateTime $start_date, \DateTime $end_date)
    {
        $candidateScores = $this->doctrine->getRepository('AppBundle:Score')
            ->getScoresByHandicapIdBetween($id, $start_date, $end_date);

        $scores = [];

        foreach ($candidateScores as $score) {
            if (!$score->getDateAccepted()) {
                continue;
            }

            $handicap = $this->calculator->handicapForScore($score);
            $scores[] = $handicap;
        }

        $newHandicaps = [];

        if (count($scores) >= 3) {
            sort($scores);
            $handicaps = array_slice($scores, 0, 3);
            $handicap = ceil(array_sum($handicaps) / 3);

            $newHandicaps[] = $this->createHandicap($id, HandicapType::REASSESS, $handicap, $end_date);
        }

        return $newHandicaps;
    }

    /**
     * @param HandicapIdentifier $id
     * @param Score[]            $scores
     *
     * @return PersonHandicap
     */
    private function initialHandicap(HandicapIdentifier $id, array $scores)
    {
        if (count($scores) !== 3) {
            throw new \InvalidArgumentException('Initial handicap should only have 3 scores');
        }

        $accum = 0;
        $date = null;

        foreach ($scores as $score) {
            $accum += $this->calculator->handicapForScore($score);
            $date = $date < $score->getDateShot() ? $score->getDateShot() : $date;

            if ($score->isIndoor() !== $id->isIndoor()) {
                throw new \InvalidArgumentException("Scores don't all have the same indoor setting");
            }

            if ($score->getBowtype() !== $id->getBowtype()) {
                throw new \InvalidArgumentException("Scores don't all have the same bowtype");
            }
        }

        $handicap = ceil($accum / 3);

        return $this->createHandicap($id, HandicapType::INITIAL, $handicap, $date);
    }

    /**
     * ASSUMPTION: $current_handicap is BEFORE $score.
     *
     * @param PersonHandicap $current_handicap
     * @param Score          $score
     *
     * @return PersonHandicap
     */
    private function averageUpdate(PersonHandicap $current_handicap, Score $score)
    {
        $score_handicap = $this->calculator->handicapForScore($score);
        $new_value = ceil(($current_handicap->getHandicap() + $score_handicap) / 2);

        if ($new_value >= $current_handicap->getHandicap()) {
            return null;
        }

        $id = new HandicapIdentifier($current_handicap->getPerson(), $score->isIndoor(), $score->getBowtype());

        return $this->createHandicap($id, HandicapType::UPDATE, $new_value, $score->getDateShot());
    }

    /**
     * @param HandicapIdentifier $id
     * @param string             $type
     * @param int                $value
     * @param \DateTime          $date
     * @param Score              $score
     *
     * @return PersonHandicap
     */
    private function createHandicap(HandicapIdentifier $id, $type, $value, $date, Score $score = null)
    {
        $handicap = new PersonHandicap();

        $handicap->setPerson($id->getPerson());
        $handicap->setHandicap($value);
        $handicap->setType($type);
        $handicap->setIndoor($id->isIndoor());
        $handicap->setBowType($id->getBowtype());
        $handicap->setDate($date);

        if ($score !== null) {
            $handicap->setScore($score);
        }

        return $handicap;
    }

    /**
     * @param PersonHandicap[] $handicaps
     */
    private function persist(array $handicaps)
    {
        $em = $this->doctrine->getManager();

        foreach ($handicaps as $handicap) {
            $em->persist($handicap);
        }

        $em->flush();
    }

    /**
     * @param HandicapIdentifier $id
     */
    private function remove(HandicapIdentifier $id)
    {
        /** @var PersonHandicap[] $handicaps */
        $handicaps = $this->doctrine->getRepository('AppBundle:PersonHandicap')
            ->findAfter($id, new \DateTime('1950-01-01'));

        $em = $this->doctrine->getManager();
        foreach ($handicaps as $handicap) {
            $em->remove($handicap);
        }
        $em->flush();
    }
}
