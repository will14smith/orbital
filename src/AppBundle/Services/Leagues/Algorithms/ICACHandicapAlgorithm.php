<?php

namespace AppBundle\Services\Leagues\Algorithms;

use AppBundle\Services\Leagues\LeagueAlgorithmInterface;

class ICACHandicapAlgorithm implements LeagueAlgorithmInterface
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
