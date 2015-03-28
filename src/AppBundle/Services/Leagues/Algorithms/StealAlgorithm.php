<?php

namespace AppBundle\Services\Leagues\Algorithms;

use AppBundle\Entity\LeaguePerson;
use AppBundle\Services\Leagues\LeagueAlgorithm;

class StealAlgorithm implements LeagueAlgorithm
{
    use RandomInitialiserTrait {
        RandomInitialiserTrait::init as init_random;
    }
    use StealScorerTrait;

    public function getKey()
    {
        return 'steal';
    }
    public function getName()
    {
        return 'Winner steals a point from the loser';
    }

    /**
     * @param LeaguePerson[] $people
     *
     * @return LeaguePerson[]
     */
    function init(array $people)
    {
        $people = $this->init_random($people);

        foreach($people as $person) {
            //TODO make configurable
            $person->setPoints(100);
        }

        return $people;
    }
}