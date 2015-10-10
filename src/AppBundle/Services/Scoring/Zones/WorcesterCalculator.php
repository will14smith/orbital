<?php

namespace AppBundle\Services\Scoring\Zones;

use AppBundle\Entity\ScoreArrow;
use AppBundle\Services\Scoring\ZoneCalculatorInterface;

class WorcesterCalculator implements ZoneCalculatorInterface
{
    /**
     * @param ScoreArrow $arrow
     *
     * @return int
     */
    function getValue(ScoreArrow $arrow)
    {
        $value = $arrow->getValue();

        if($value == 'M') return 0;

        $int = intval($value);

        if($int < 1 || $int > 5) {
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
        return $this->getValue($arrow) == 5;
    }
}
