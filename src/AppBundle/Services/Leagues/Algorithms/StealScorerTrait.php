<?php

namespace AppBundle\Services\Leagues\Algorithms;

trait StealScorerTrait
{
    /**
     * @return \int[] [winner delta, loser delta]
     */
    public function score()
    {
        return [1, -1];
    }
}
