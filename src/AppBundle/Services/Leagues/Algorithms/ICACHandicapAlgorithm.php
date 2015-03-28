<?php

namespace AppBundle\Services\Leagues\Algorithms;

use AppBundle\Services\Leagues\LeagueAlgorithm;

class ICACHandicapAlgorithm implements LeagueAlgorithm
{
    use HandicapInitialiserTrait;
    use ICACScorerTrait;

    public function getKey()
    {
        return 'icac-hc';
    }

    public function getName()
    {
        return 'ICAC (handicap init)';
    }
}