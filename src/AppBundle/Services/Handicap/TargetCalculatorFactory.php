<?php

namespace AppBundle\Services\Handicap;

use AppBundle\Entity\RoundTarget;
use AppBundle\Services\Enum\ScoreZones;
use AppBundle\Services\Handicap\Calculators\ImperialTargetCalculator;
use AppBundle\Services\Handicap\Calculators\MetricTargetCalculator;

class TargetCalculatorFactory
{
    /**
     * @param RoundTarget $roundTarget
     * @param boolean $compound
     *
     * @return TargetCalculatorInterface
     * @throws \Exception
     */
    public static function create(RoundTarget $roundTarget, $compound)
    {
        switch ($roundTarget->getScoringZones()) {
            case ScoreZones::METRIC:
                return new MetricTargetCalculator($compound);
            case ScoreZones::IMPERIAL:
                return new ImperialTargetCalculator();
            default:
                throw new \Exception("Failed to create target calculator");
        }
    }
}
