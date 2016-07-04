<?php

namespace AppBundle\Services\Leagues\Algorithms;

use AppBundle\Entity\LeaguePerson;
use AppBundle\Services\Leagues\LeagueAlgorithmInterface;

class StealAlgorithm implements LeagueAlgorithmInterface
{
    use RandomInitialiserTrait {
        RandomInitialiserTrait::init as randomInit;
    }
    use StealScorerTrait;

    private $initialPoints;

    public function __construct($initialPoints)
    {
        $this->initialPoints = intval($initialPoints);
    }

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
    public function init(array $people)
    {
        $people = $this->randomInit($people);

        foreach ($people as $person) {
            $person->setPoints($this->initialPoints);
        }

        return $people;
    }
}
