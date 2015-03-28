<?php

namespace AppBundle\Services\Leagues;

interface LeagueAlgorithm extends LeagueInitialiser, LeagueScorer {
    public function getKey();
    public function getName();
}