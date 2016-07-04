<?php

namespace AppBundle\Services\Leagues\Algorithms;

use AppBundle\Entity\LeagueMatch;

trait StealScorerTrait
{
    /**
     * @param LeagueMatch $match
     *
     * @return \int[] [winner delta, loser delta]
     */
    public function score(LeagueMatch $match)
    {
        return [1, -1];
    }
}
