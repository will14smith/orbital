<?php

namespace AppBundle\Services\Leagues\Algorithms;

use AppBundle\Services\Leagues\LeagueAlgorithmInterface;

class ICACAlgorithm implements LeagueAlgorithmInterface
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
