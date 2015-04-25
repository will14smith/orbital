<?php

namespace AppBundle\Tests\Services\Leagues;

use AppBundle\Entity\LeagueMatch;
use AppBundle\Services\Leagues\LeagueAlgorithmInterface;

class LeagueAlgorithmStub implements LeagueAlgorithmInterface
{
    /**
     * @var int
     */
    private $deltaWinner;
    /**
     * @var int
     */
    private $deltaLoser;

    public function __construct($deltaWinner, $deltaLoser)
    {
        $this->deltaWinner = $deltaWinner;
        $this->deltaLoser = $deltaLoser;
    }

    public function getKey()
    {
        return "dummy";
    }

    public function getName()
    {
        return "dummy";
    }

    function init(array $people)
    {
        throw new \Exception();
    }

    /**
     * @param LeagueMatch $match
     *
     * @return int[] [winner dpoints, loser dpoints]
     */
    function score(LeagueMatch $match)
    {
        return [$this->deltaWinner, $this->deltaLoser];
    }
}
