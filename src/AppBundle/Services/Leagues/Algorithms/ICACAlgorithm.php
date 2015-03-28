<?php

namespace AppBundle\Services\Leagues\Algorithms;

use AppBundle\Services\Leagues\LeagueAlgorithm;

class ICACAlgorithm implements LeagueAlgorithm
{
    use RandomInitialiserTrait;
    use ICACScorerTrait;

    public function getKey()
    {
        return 'icac';
    }
    public function getName()
    {
        return 'ICAC (random init)';
    }
}

