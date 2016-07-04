<?php

namespace AppBundle\Services\Handicap\Calculators;

use AppBundle\Services\Handicap\TargetCalculatorInterface;

class WorcesterTargetCalculator implements TargetCalculatorInterface
{
    public function calculate($sigma, $targetDiameter)
    {
        $sum = 0;

        $sigma_sq = pow($sigma, 2);

        for ($n = 1; $n <= 5; ++$n) {
            $x = pow($n * $targetDiameter / 10 + 0.357, 2);

            $sum += exp(-$x / $sigma_sq);
        }

        return 5 - $sum;
    }
}
