<?php

namespace AppBundle\Services\Handicap;

use AppBundle\Entity\Round;
use AppBundle\Entity\RoundTarget;
use AppBundle\Entity\Score;
use AppBundle\Services\Enum\BowType;

class ClassificationCalculator
{
    /**
     * @var HandicapCalculator
     */
    private $calculator;

    public function __construct(HandicapCalculator $calculator)
    {
        $this->calculator = $calculator;
    }

    public function calculateRoundScore(Round $round, $gender, $bowtype, $classification)
    {
        $handicap = ClassificationTable::getHandicap($gender, $bowtype, $classification);

        $score = $this->calculator->score($round, $bowtype == BowType::COMPOUND, $handicap);

        return $score;
    }

    public function calculateTargetScore(RoundTarget $target, $gender, $bowtype, $classification)
    {
        $handicap = ClassificationTable::getHandicap($gender, $bowtype, $classification);

        $score = $this->calculator->scoreTarget($target, $bowtype == BowType::COMPOUND, $handicap);

        return $score;
    }

    public function calculateClassification(Score $score)
    {
        throw new \Exception('Not Implemented');
    }

    public function isValidClassifiation($round, $gender, $bowtype, $classification)
    {
        // TODO
        return true;
    }
}
