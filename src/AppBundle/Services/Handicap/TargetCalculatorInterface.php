<?php

namespace AppBundle\Services\Handicap;

interface TargetCalculatorInterface
{
    public function calculate($sigma, $targetDiameter);
}
