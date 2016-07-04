<?php

namespace AppBundle\Services\Leagues;

use AppBundle\Entity\LeagueMatch;

interface LeagueScorerInterface
{
    /**
     * @param LeagueMatch $match
     *
     * @return int[] [winner dpoints, loser dpoints]
     */
    public function score(LeagueMatch $match);
}
