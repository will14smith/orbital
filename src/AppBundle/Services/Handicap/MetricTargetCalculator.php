<?php


namespace AppBundle\Services\Handicap;


class MetricTargetCalculator implements TargetCalculator
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

    public function calculate($sigma, $target)
    {
        $sum = 0;

        $sigma_sq = pow($sigma, 2);

        if ($this->compound) {
            $x = pow($target / 40 + 0.357, 2);
            $sum = exp(-$x / $sigma_sq);

            $start = 2;
        } else {
            $start = 1;
        }

        for ($n = $start; $n <= 10; $n++) {
            $x = pow($n * $target / 20 + 0.357, 2);

            $sum += exp(-$x / $sigma_sq);
        }

        return 10 - $sum;
    }
}