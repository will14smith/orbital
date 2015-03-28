<?php

namespace AppBundle\Services\Leagues\Algorithms;

use AppBundle\Entity\LeagueMatch;

trait ICACScorerTrait
{
    /**
     * @param LeagueMatch $match
     *
     * @return int[] [winner dpoints, loser dpoints]
     */
    public function score(LeagueMatch $match)
    {
        $winner = $match->getWinner();
        $loser = $match->getLoser();

        $delta = $winner->getPoints() - $loser->getPoints();

        return [$this->lookup_points($delta), 0];
    }

    /**
     * using the table from ICAC rules of 2014-2015
     * @param int $delta
     *
     * @return int
     */
    private function lookup_points($delta) {
        if ($delta >= 20) {
            return 1;
        }
        if ($delta >= 15) {
            return 4;
        }
        if ($delta >= 10) {
            return 7;
        }
        if ($delta >= 4) {
            return 10;
        }
        if ($delta >= -3) {
            return 13;
        }
        if ($delta >= -9) {
            return 16;
        }
        if ($delta >= -14) {
            return 19;
        }
        if ($delta >= -19) {
            return 22;
        }

        return 25;
    }
}