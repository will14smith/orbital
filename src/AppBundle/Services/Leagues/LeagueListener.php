<?php


namespace AppBundle\Services\Leagues;

use AppBundle\Entity\LeagueMatch;
use AppBundle\Services\Events\LeagueMatchEvent;

class LeagueListener
{
    /**
     * @var LeagueManager
     */
    private $manager;

    public function __construct(LeagueManager $manager)
    {

        $this->manager = $manager;
    }

    public function match_create(LeagueMatchEvent $event)
    {
        $match = $event->getMatch();

        if ($this->should_accept($match)) {
            $this->manager->handleMatch($match);
        }
    }

    public function match_update(LeagueMatchEvent $event)
    {
        $match = $event->getMatch();

        if ($this->should_accept($match)) {
            $this->manager->handleMatch($match);
        }
    }

    /**
     * @param LeagueMatch $match
     *
     * @return bool
     *
     */
    private function should_accept(LeagueMatch $match)
    {
        if(!$match->getAccepted()) {
            return false;
        }

        if(!$match->getDateConfirmed()) {
            return false;
        }

        if(!$match->getLeague()->getAlgoName()) {
            return false;
        }

        return true;
    }
}