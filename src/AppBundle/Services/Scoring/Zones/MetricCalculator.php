<?php

namespace AppBundle\Services\Scoring\Zones;

use AppBundle\Entity\ScoreArrow;
use AppBundle\Services\Scoring\ZoneCalculator;

class MetricCalculator implements ZoneCalculator
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

        return intval($value);
    }

    /**
     * @param ScoreArrow $arrow
     *
     * @return bool
     */
    function isHit(ScoreArrow $arrow)
    {
        return $arrow->getValue() != 'M';
    }

    /**
     * @param ScoreArrow $arrow
     *
     * @return bool
     */
    function isGold(ScoreArrow $arrow)
    {
        //TODO is this correct?? (for all bow types)

        return $this->getValue($arrow) >= 9;
    }
}
