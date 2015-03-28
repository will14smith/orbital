<?php


namespace AppBundle\Services\Handicap;


use AppBundle\Entity\RoundTarget;
use AppBundle\Services\Enum\ScoreZones;

class TargetCalculatorFactory
{
    /**
     * @param RoundTarget $roundTarget
     * @param boolean $compound
     *
     * @return TargetCalculator
     * @throws \Exception
     */
    public static function create(RoundTarget $roundTarget, $compound)
    {
        switch ($roundTarget->getScoringZones()) {
            case ScoreZones::METRIC:
                return new MetricTargetCalculator($compound);
            default:
                throw new \Exception("Failed to create target calculator");
        }
    }
}