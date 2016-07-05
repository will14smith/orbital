<?php

namespace AppBundle\Services\Handicap;

use AppBundle\Entity\PersonHandicap;
use AppBundle\Entity\Score;

class HandicapDeciderResult
{
    /**
     * @var PersonHandicap
     */
    private $handicap;
    /**
     * @var Score[]
     */
    private $remaining_scores;

    /**
     * HandicapDeciderResult constructor.
     *
     * @param PersonHandicap $handicap
     * @param Score[]        $remaining_scores
     */
    private function __construct(PersonHandicap $handicap = null, array $remaining_scores)
    {
        $this->handicap = $handicap;
        $this->remaining_scores = $remaining_scores;
    }

    // getters

    /**
     * @return bool
     */
    public function hasHandicap()
    {
        return $this->handicap !== null;
    }

    /**
     * @return PersonHandicap
     */
    public function getHandicap()
    {
        return $this->handicap;
    }

    /**
     * @return \AppBundle\Entity\Score[]
     */
    public function getRemainingScores()
    {
        return $this->remaining_scores;
    }

    // static constructors

    /**
     * @param Score[] $scores
     *
     * @return HandicapDeciderResult
     */
    public static function none(array $scores)
    {
        return new self(null, $scores);
    }

    /**
     * @param PersonHandicap $handicap
     * @param Score[]        $remaining_scores
     *
     * @return HandicapDeciderResult
     */
    public static function success(PersonHandicap $handicap, array $remaining_scores)
    {
        return new self($handicap, $remaining_scores);
    }
}
