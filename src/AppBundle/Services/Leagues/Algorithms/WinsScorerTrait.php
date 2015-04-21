<?php

namespace AppBundle\Services\Leagues\Algorithms;


use AppBundle\Entity\LeagueMatch;

trait WinsScorerTrait
{
    /**
     * @param LeagueMatch $match
     *
     * @return int[] [winner dpoints, loser dpoints]
     */
    public function score(LeagueMatch $match)
    {
        return [1, 0];
    }
}
