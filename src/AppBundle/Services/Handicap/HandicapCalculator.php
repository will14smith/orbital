<?php


namespace AppBundle\Services\Handicap;

// http://www.roystonarchery.org/new/wp-content/uploads/2013/09/Graduated-Handicap-Tables.pdf
class HandicapCalculator
{
    public function sigma($range, $handicap)
    {
        $pinhole = 100 * $range * pow(1.036, $handicap + 12.9) * 5 * pow(10, -4);
        $k = 1.429 * pow(10, -6) * pow(1.07, $handicap + 4.3);
        $f = 1 + $k * pow($range, 2);

        return $pinhole * $f;
    }

    public function average(HandicapArrowCalculator $calculator, $range, $target, $handicap)
    {
        $sigma = $this->sigma($range, $handicap);

        return $calculator->calculate($sigma, $target);
    }

    public function score(HandicapArrowCalculator $calculator, $round, $handicap)
    {
        $score = 0;

        foreach($round as $rt) {
            $score += $rt['count'] * $this->average($calculator, $rt['range'], $rt['target'], $handicap);
        }

        return $score;
    }
}