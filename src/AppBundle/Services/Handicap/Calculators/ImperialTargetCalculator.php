<?php

namespace AppBundle\Services\Handicap\Calculators;

use AppBundle\Services\Handicap\TargetCalculatorInterface;

class ImperialTargetCalculator implements TargetCalculatorInterface
{
    public function calculate($sigma, $targetDiameter)
    {
        $sum = 0;
        $start = 1;

        $sigma_sq = pow($sigma, 2);

        for ($n = $start; $n <= 4; $n++) {
            $x = pow($n * $targetDiameter / 10 + 0.357, 2);

            $sum += exp(-$x / $sigma_sq);
        }

        $x = pow($targetDiameter / 2 + 0.357, 2);
        $alpha = exp(-$x / $sigma_sq);

        return 9 - 2 * $sum - $alpha;
    }
}
