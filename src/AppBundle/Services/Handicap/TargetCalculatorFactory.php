<?php

namespace AppBundle\Services\Handicap;

use AppBundle\Entity\RoundTarget;
use AppBundle\Services\Enum\ScoreZones;
use AppBundle\Services\Handicap\Calculators\ImperialTargetCalculator;
use AppBundle\Services\Handicap\Calculators\Metric5ZoneTargetCalculator;
use AppBundle\Services\Handicap\Calculators\MetricTargetCalculator;
use AppBundle\Services\Handicap\Calculators\WorcesterTargetCalculator;

class TargetCalculatorFactory
{
    /**
     * @param RoundTarget $roundTarget
     * @param bool        $useInnerTen
     *
     * @return TargetCalculatorInterface
     *
     * @throws \Exception
     */
    public static function create(RoundTarget $roundTarget, $useInnerTen)
    {
        switch ($roundTarget->getScoringZones()) {
            case ScoreZones::METRIC:
                return new MetricTargetCalculator($useInnerTen);
            case ScoreZones::TRIPLE:
                return new Metric5ZoneTargetCalculator($useInnerTen);
            case ScoreZones::IMPERIAL:
                return new ImperialTargetCalculator();
            case ScoreZones::WORCESTER:
                return new WorcesterTargetCalculator();
            default:
                throw new \Exception('Failed to create target calculator');
        }
    }
}
