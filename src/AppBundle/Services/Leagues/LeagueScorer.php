<?php

namespace AppBundle\Services\Leagues;


use AppBundle\Entity\LeagueMatch;

interface LeagueScorer {
    /**
     * @param LeagueMatch $match
     *
     * @return int[] [winner dpoints, loser dpoints]
     */
    function score(LeagueMatch $match);
}
