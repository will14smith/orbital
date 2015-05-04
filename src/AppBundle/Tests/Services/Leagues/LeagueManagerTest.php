<?php

namespace AppBundle\Tests\Services\Leagues;

use AppBundle\Entity\League;
use AppBundle\Entity\LeagueMatch;
use AppBundle\Entity\LeaguePerson;
use AppBundle\Services\Events\LeagueMatchEvent;
use AppBundle\Services\Leagues\LeagueListener;
use AppBundle\Services\Leagues\LeagueManager;
use AppBundle\Tests\Services\ServiceTestCase;

class LeagueManagerTest extends ServiceTestCase
{
    private function getMatch()
    {
        $league = new League();
        $league->setAlgoName('dummy');

        $match = new LeagueMatch();
        $match->setAccepted(true);
        $match->setDateConfirmed(new \DateTime('now'));
        $match->setLeague($league);

        $match->setChallenger(new LeaguePerson());
        $match->setChallengee(new LeaguePerson());

        $match->setAccepted(true);
        $match->setResult(true);
        $match->setDateConfirmed(new \DateTime('now'));

        return $match;
    }

    private function getManager($flushCount = null)
    {
        $doctrine = $this->getDoctrine($flushCount);
        $manager = new LeagueManager($doctrine);

        $manager->addAlgorithm(new LeagueAlgorithmStub(17, -13));

        return $manager;
    }

    public function testUpdateMatch()
    {
        $manager = $this->getManager();
        $match = $this->getMatch();

        $manager->updateMatch($match);

        $this->assertEquals(17, $match->getWinner()->getPoints());
        $this->assertEquals(-13, $match->getLoser()->getPoints());
    }

    public function testHandleMatch()
    {
        $manager = $this->getManager(1);
        $match = $this->getMatch();

        $manager->handleMatch($match);
    }
}
