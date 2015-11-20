<?php

namespace AppBundle\Tests\Services\Leagues;

use AppBundle\Entity\League;
use AppBundle\Entity\LeagueMatch;
use AppBundle\Services\Events\LeagueMatchEvent;
use AppBundle\Services\Leagues\LeagueListener;
use AppBundle\Services\Leagues\LeagueManager;
use AppBundle\Tests\Services\ServiceTestCase;

class LeagueListenerTest extends ServiceTestCase {
    private function getListener($count) {
        /** @var LeagueManager|\PHPUnit_Framework_MockObject_MockObject $manager */
        $manager = $this->getMockBuilder('\AppBundle\Services\Leagues\LeagueManager')->getMock();

        $manager->expects($this->exactly($count))
            ->method("handleMatch");

        return new LeagueListener($manager);
    }

    private function getMatch() {
        $league = new League();
        $league->setAlgoName('dummy');

        $match = new LeagueMatch();
        $match->setAccepted(true);
        $match->setDateConfirmed(new \DateTime('now'));
        $match->setLeague($league);

        return $match;
    }

    public function testCreateValid() {
        $listener = $this->getListener(1);

        $match = $this->getMatch();

        $listener->match_create(new LeagueMatchEvent($match));
    }
    public function testCreateNotAccepted() {
        $listener = $this->getListener(0);

        $match = $this->getMatch();
        $match->setAccepted(false);

        $listener->match_create(new LeagueMatchEvent($match));
    }
    public function testCreateNotConfirmed() {
        $listener = $this->getListener(0);

        $match = $this->getMatch();
        $match->setDateConfirmed(null);

        $listener->match_create(new LeagueMatchEvent($match));
    }
    public function testCreateNoAlgo() {
        $listener = $this->getListener(0);

        $match = $this->getMatch();
        $match->getLeague()->setAlgoName(null);

        $listener->match_create(new LeagueMatchEvent($match));
    }

    public function testUpdateValid() {
        $listener = $this->getListener(1);

        $match = $this->getMatch();

        $listener->match_update(new LeagueMatchEvent($match));
    }
    public function testUpdateNotAccepted() {
        $listener = $this->getListener(0);

        $match = $this->getMatch();
        $match->setAccepted(false);

        $listener->match_update(new LeagueMatchEvent($match));
    }
    public function testUpdateNotConfirmed() {
        $listener = $this->getListener(0);

        $match = $this->getMatch();
        $match->setDateConfirmed(null);

        $listener->match_update(new LeagueMatchEvent($match));
    }
    public function testUpdateNoAlgo() {
        $listener = $this->getListener(0);

        $match = $this->getMatch();
        $match->getLeague()->setAlgoName(null);

        $listener->match_update(new LeagueMatchEvent($match));
    }
}
