<?php
/**
 * Created by PhpStorm.
 * User: will_000
 * Date: 29/03/2015
 * Time: 22:54
 */

namespace AppBundle\Services\Scoring;


use AppBundle\Entity\ScoreArrow;

interface ZoneCalculator
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