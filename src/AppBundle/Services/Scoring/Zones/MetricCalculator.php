<?php

namespace AppBundle\Services\Scoring\Zones;

use AppBundle\Entity\ScoreArrow;
use AppBundle\Services\Scoring\ZoneCalculatorInterface;

class MetricCalculator implements ZoneCalculatorInterface
{
    /**
     * @param ScoreArrow $arrow
     *
     * @return int
     */
    function getValue(ScoreArrow $arrow)
    {
        $value = $arrow->getValue();

        if($value == 'X') return 10;
        if($value == 'M') return 0;

        $int = intval($value);

        if($int < 1 || $int > 10) {
            throw new \InvalidArgumentException();
        }

        return $int;
    }

    /**
     * @param ScoreArrow $arrow
     *
     * @return bool
     */
    function isHit(ScoreArrow $arrow)
    {
        return $this->getValue($arrow) > 0;
    }

    /**
     * @param ScoreArrow $arrow
     *
     * @return bool
     */
    function isGold(ScoreArrow $arrow)
    {
        //TODO is this correct?? (for all bow types)

        return $this->getValue($arrow) == 10;
    }
}
