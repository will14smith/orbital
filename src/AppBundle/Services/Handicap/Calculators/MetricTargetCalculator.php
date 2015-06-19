<?php

namespace AppBundle\Services\Handicap\Calculators;

use AppBundle\Services\Handicap\TargetCalculatorInterface;

class MetricTargetCalculator implements TargetCalculatorInterface
{
    /**
     * @var boolean
     */
    private $compound;

    /**
     * @param boolean $compound
     */
    public function __construct($compound)
    {

        $this->compound = $compound;
    }

    public function calculate($sigma, $targetDiameter)
    {
        $sum = 0;

        $sigma_sq = pow($sigma, 2);

        if ($this->compound) {
            $x = pow($targetDiameter / 40 + 0.357, 2);
            $sum = exp(-$x / $sigma_sq);

            $start = 2;
        } else {
            $start = 1;
        }

        for ($n = $start; $n <= 10; $n++) {
            $x = pow($n * $targetDiameter / 20 + 0.357, 2);

            $sum += exp(-$x / $sigma_sq);
        }

        return 10 - $sum;
    }
}
