<?php

namespace AppBundle\Services\Leagues\Algorithms;

use AppBundle\Services\Leagues\LeagueAlgorithm;

class WinsAlgorithm implements LeagueAlgorithm {
    use RandomInitialiserTrait;
    use WinsScorerTrait;

    public function getKey()
    {
        return 'wins';
    }
    public function getName()
    {
        return 'Winner gets a point';
    }
}
