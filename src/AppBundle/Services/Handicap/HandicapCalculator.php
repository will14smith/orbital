<?php


namespace AppBundle\Services\Handicap;

// http://www.roystonarchery.org/new/wp-content/uploads/2013/09/Graduated-Handicap-Tables.pdf
use AppBundle\Entity\Round;
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

    private function average(TargetCalculator $calculator, $range, $target, $handicap)
    {
        $sigma = $this->sigma($range, $handicap);

        return $calculator->calculate($sigma, $target);
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
            $range = Unit::convert($rt->getDistanceValue(), $rt->getDistanceUnit(), Unit::METER);
            $target = Unit::convert($rt->getTargetValue(), $rt->getTargetUnit(), Unit::CENTIMETER);

            $calculator = TargetCalculatorFactory::create($rt, $compound);

            $score += $rt->getArrowCount() *
                $this->average($calculator, $range, $target, $handicap);
        }

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
        $point = 50;

        while ($delta >= 1) {
            $point_score = $this->score($round, $compound, $point);

            if ($score < $point_score) {
                $point += $delta;
            } else if ($score > $point_score) {
                $point -= $delta;
            } else return $point;

            $delta /= 2;
        }

        return $point;
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
