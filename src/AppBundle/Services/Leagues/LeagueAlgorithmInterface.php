<?php

namespace AppBundle\Services\Leagues;

interface LeagueAlgorithmInterface extends LeagueInitialiserInterface, LeagueScorerInterface {
    public function getKey();
    public function getName();
}
