<?php

namespace AppBundle\Services\Leagues\Algorithms;

use AppBundle\Entity\LeagueMatch;

trait StealScorerTrait
{
    /**
     * @param LeagueMatch $match
     *
     * @return int[] [winner dpoints, loser dpoints]
     */
    public function score(LeagueMatch $match)
    {
        return [1, -1];
    }
}