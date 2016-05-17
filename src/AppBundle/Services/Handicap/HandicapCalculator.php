<?php


namespace AppBundle\Services\Handicap;

// http://www.roystonarchery.org/new/wp-content/uploads/2013/09/Graduated-Handicap-Tables.pdf
use AppBundle\Entity\Round;
use AppBundle\Entity\RoundTarget;
use AppBundle\Entity\Score;
use AppBundle\Services\Enum\BowType;
use AppBundle\Services\Enum\Unit;

class HandicapCalculator
{
    private function sigma($range, $handicap)
    {
        $pinhole = 100 * $range * pow(1.036, $handicap + 12.9) * 5 * pow(10, -4);
        $k = 1.429 * pow(10, -6) * pow(1.07, $handicap + 4.3);
        $f = 1 + $k * pow($range, 2);

        return $pinhole * $f;
    }

    private function average(TargetCalculatorInterface $calculator, $range, $targetDiameter, $handicap)
    {
        $sigma = $this->sigma($range, $handicap);

        return $calculator->calculate($sigma, $targetDiameter);
    }

    /**
     * Get the lowest score needed for the given handicap
     *
     * @param Round $round
     * @param boolean $compound
     * @param int $handicap
     *
     * @return int
     * @throws \Exception
     */
    public function score(Round $round, $compound, $handicap)
    {
        $score = 0;

        foreach ($round->getTargets() as $rt) {
            $score += $this->scoreTarget($rt, $compound, $handicap);
        }

        return round($score);
    }

    public function scoreTarget(RoundTarget $target, $compound, $handicap) {
        $range = Unit::convert($target->getDistanceValue(), $target->getDistanceUnit(), Unit::METER);
        $targetDiameter = Unit::convert($target->getTargetValue(), $target->getTargetUnit(), Unit::CENTIMETER);

        $calculator = TargetCalculatorFactory::create($target, $compound);

        $averageScore = $this->average($calculator, $range, $targetDiameter, $handicap);
        $score = $averageScore * $target->getArrowCount();

        return $score;
    }

    /**
     * Get the handicap for the given score
     *
     * @param Round $round
     * @param boolean $compound
     * @param int $score
     *
     * @return int
     */
    public function handicap(Round $round, $compound, $score)
    {
        // use a binary search method
        $delta = 32;
        $handicap = 50;

        while ($delta >= 1) {
            $hc_min_score = $this->score($round, $compound, $handicap);

            if ($score < $hc_min_score) {
                $handicap += $delta;
            } else if ($score > $hc_min_score) {
                $handicap -= $delta;
            } else {
                return $handicap;
            }

            $delta /= 2;
        }

        // fix off by 1 error
        $hc_min_score = $this->score($round, $compound, $handicap);

        return $score < $hc_min_score ? $handicap + 1 : $handicap;
    }

    public function handicapForScore(Score $score)
    {
        return $this->handicap(
            $score->getRound(),
            $score->getBowtype() == BowType::COMPOUND,
            $score->getScore()
        );
    }
}
