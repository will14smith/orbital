<?php

namespace AppBundle\Services\Scoring;

use AppBundle\Entity\ScoreArrow;

interface ZoneCalculatorInterface
{
    /**
     * @param ScoreArrow $arrow
     *
     * @return int
     */
    function getValue(ScoreArrow $arrow);

    /**
     * @param ScoreArrow $arrow
     *
     * @return bool
     */
    function isHit(ScoreArrow $arrow);

    /**
     * @param ScoreArrow $arrow
     *
     * @return bool
     */
    function isGold(ScoreArrow $arrow);
}
