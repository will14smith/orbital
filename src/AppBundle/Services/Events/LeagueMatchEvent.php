<?php

namespace AppBundle\Services\Events;

use AppBundle\Entity\LeagueMatch;
use Symfony\Component\EventDispatcher\Event;

class LeagueMatchEvent extends Event
{
    /**
     * @var LeagueMatch
     */
    private $match;

    public function __construct(LeagueMatch $match)
    {
        $this->match = $match;
    }

    /**
     * @return LeagueMatch
     */
    public function getMatch()
    {
        return $this->match;
    }
}
