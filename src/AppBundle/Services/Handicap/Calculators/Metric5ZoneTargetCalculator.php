<?php

namespace AppBundle\Services\Handicap\Calculators;

use AppBundle\Services\Handicap\TargetCalculatorInterface;

class Metric5ZoneTargetCalculator implements TargetCalculatorInterface
{
    /**
     * @var bool
     */
    private $useInnerTen;

    /**
     * @param bool $useInnerTen
     */
    public function __construct($useInnerTen)
    {
        $this->useInnerTen = $useInnerTen;
    }

    public function calculate($sigma, $targetDiameter)
    {
        $s = 10;

        $sigma_sq = pow($sigma, 2);

        for ($i = 1; $i < 5; ++$i) {
            $s -= exp(-pow(($i * $targetDiameter) / ($i == 1 && $this->useInnerTen ? 40 : 20) + 0.357, 2) / $sigma_sq);
        }

        $s -= 6 * exp(-pow((5 * $targetDiameter) / 20 + 0.357, 2) / $sigma_sq);

        return $s;
    }
}
