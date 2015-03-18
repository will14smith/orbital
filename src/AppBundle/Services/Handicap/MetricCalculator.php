<?php


namespace AppBundle\Services\Handicap;


class MetricCalculator implements HandicapArrowCalculator
{
    function calculate($sigma, $target)
    {
        $sum = 0;

        for ($n = 1; $n <= 10; $n++) {
            $x = pow($n * $target / 20 + 0.357, 2);

            $sum += exp(-$x / pow($sigma, 2));
        }

        return 10 - $sum;
    }
}