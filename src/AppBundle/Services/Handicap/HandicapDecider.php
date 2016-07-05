<?php

namespace AppBundle\Services\Handicap;


use AppBundle\Entity\PersonHandicap;
use AppBundle\Entity\Score;
use AppBundle\Services\Enum\HandicapType;

class HandicapDecider
{
    /**
     * @var HandicapCalculator
     */
    private $calculator;
    /**
     * @var ReassessmentRepositoryInterface
     */
    private $reassessmentRepository;

    public function __construct(HandicapCalculator $calculator, ReassessmentRepositoryInterface $reassessmentRepository)
    {
        $this->calculator = $calculator;
        $this->reassessmentRepository = $reassessmentRepository;
    }

    /**
     * @param HandicapIdentifier $id
     * @param PersonHandicap     $current_handicap
     * @param Score[]            $scores
     *
     * @return HandicapDeciderResult
     */
    public function decide(HandicapIdentifier $id, PersonHandicap $current_handicap = null, array $scores)
    {
        if ($current_handicap === null) {
            return $this->initial($id, $scores);
        }

        $current_handicap_season = $this->getSeasonStart($id, $current_handicap->getDate());

        if (count($scores) === 0) {
            $current_date_season = $this->getSeasonStart($id, new \DateTime('now'));

            if ($current_date_season == $current_handicap_season) {
                return HandicapDeciderResult::none([]);
            }

            return $this->reassessment($id, $current_handicap_season, $scores);
        }

        /** @var Score $first_score */
        $first_score = $scores[0];
        $first_score_season = $this->getSeasonStart($id, $first_score->getDateShot());

        if ($first_score_season != $current_handicap_season) {
            $result = $this->reassessment($id, $current_handicap_season, $scores);
            if ($result->hasHandicap()) {
                return $result;
            }
        }

        return $this->averageUpdate($id, $current_handicap, $scores);
    }

    /**
     * @param HandicapIdentifier $id
     * @param Score[]            $scores
     *
     * @return HandicapDeciderResult
     */
    private function initial(HandicapIdentifier $id, array $scores)
    {
        if (count($scores) < 3) {
            return HandicapDeciderResult::none([]);
        }

        // check scores are in same season (remove if < 3 in season)
        $season = $this->getSeasonStart($id, $scores[0]->getDateShot());
        if ($season != $this->getSeasonStart($id, $scores[1]->getDateShot())) {
            return HandicapDeciderResult::none(array_slice($scores, 1));
        }
        if ($season != $this->getSeasonStart($id, $scores[2]->getDateShot())) {
            return HandicapDeciderResult::none(array_slice($scores, 2));
        }

        /** @var Score[] $initial_scores */
        $initial_scores = array_slice($scores, 0, 3);
        /** @var Score[] $remaining_scores */
        $remaining_scores = array_slice($scores, 3);

        $handicap_acc = 0;
        foreach ($initial_scores as $score) {
            $handicap_acc += $this->calculator->handicapForScore($score);
        }

        $handicap_value = ceil($handicap_acc / 3);
        $handicap = $this->createHandicap($id, HandicapType::INITIAL, $handicap_value, $initial_scores[2]->getDateShot(), null);

        return HandicapDeciderResult::success($handicap, $remaining_scores);
    }

    /**
     * @param HandicapIdentifier $id
     * @param \DateTime          $season_start_date
     * @param Score[]            $remaining_scores
     *
     * @return HandicapDeciderResult
     */
    private function reassessment(HandicapIdentifier $id, \DateTime $season_start_date, array $remaining_scores)
    {
        $season_end_date = clone $season_start_date;
        $season_end_date->add(new \DateInterval('P1Y'));

        $scores = $this->reassessmentRepository->getScores($id, $season_start_date, $season_end_date);

        if (count($scores) < 3) {
            return HandicapDeciderResult::none($remaining_scores);
        }

        /** @var int[] $handicaps */
        $handicaps = array_map(function ($score) {
            return $this->calculator->handicapForScore($score);
        }, $scores);

        sort($handicaps, SORT_NUMERIC);

        $handicap_acc = array_sum(array_slice($handicaps, 0, 3));
        $handicap_value = ceil($handicap_acc / 3);

        $handicap = $this->createHandicap($id, HandicapType::REASSESS, $handicap_value, $season_end_date, null);

        return HandicapDeciderResult::success($handicap, $remaining_scores);
    }

    /**
     * @param HandicapIdentifier $id
     * @param PersonHandicap     $current_handicap
     * @param Score[]            $scores
     *
     * @return HandicapDeciderResult
     */
    private function averageUpdate(HandicapIdentifier $id, PersonHandicap $current_handicap, array $scores)
    {
        $score = $scores[0];
        $remaining_scores = array_slice($scores, 1);

        $score_handicap = $this->calculator->handicapForScore($score);
        $handicap_value = ceil(($current_handicap->getHandicap() + $score_handicap) / 2);

        if ($handicap_value >= $current_handicap->getHandicap()) {
            return HandicapDeciderResult::none($remaining_scores);
        }

        $handicap = $this->createHandicap($id, HandicapType::UPDATE, $handicap_value, $score->getDateShot(), $score);

        return HandicapDeciderResult::success($handicap, $remaining_scores);
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
     * @param HandicapIdentifier $id
     * @param \DateTime          $date
     *
     * @return \DateTime
     */
    private function getSeasonStart(HandicapIdentifier $id, \DateTime $date)
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
}
