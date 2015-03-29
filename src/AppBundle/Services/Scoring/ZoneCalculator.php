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
    function getValue(ScoreArrow $arrow);
    function isHit(ScoreArrow $arrow);
    function isGold(ScoreArrow $arrow);
}