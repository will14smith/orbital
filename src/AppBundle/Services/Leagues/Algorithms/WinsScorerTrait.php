<?php

namespace AppBundle\Services\Leagues\Algorithms;

trait WinsScorerTrait
{
    /**
     * @return int[] [winner delta, loser delta]
     */
    public function score()
    {
        return [1, 0];
    }
}
