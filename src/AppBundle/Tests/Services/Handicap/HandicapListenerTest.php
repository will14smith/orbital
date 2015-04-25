<?php

namespace AppBundle\Tests\Services\Handicap;

use AppBundle\Entity\Score;
use AppBundle\Services\Events\ScoreEvent;
use AppBundle\Services\Handicap\HandicapListener;
use AppBundle\Tests\Services\BaseTestCase;

class HandicapListenerTest extends BaseTestCase
{
    private function createManager() {
        return $this->getMockBuilder('\AppBundle\Services\Handicap\HandicapManager')->getMock();
    }

    private function createListener() {
        $manager = $this->createManager();
        $doctrine = $this->getDoctrine();
        $listener = new HandicapListener($manager, $doctrine);

        return [$listener, $manager, $doctrine];
    }

    private function getValidScore() {
        $score = new Score();

        $score->setComplete(true);
        $score->setDateAccepted(new \DateTime('10 seconds ago'));

        return $score;
    }

    public function testCreateScoreValid() {
        list($listener, $manager) = $this->createListener();

        $manager->expects($this->once())
            ->method('updateHandicap');

        $score = $this->getValidScore();
        $listener->score_create(new ScoreEvent($score));
    }
    public function testCreateScoreIncomplete() {
        list($listener, $manager) = $this->createListener();

        $manager->expects($this->never())
            ->method('updateHandicap');

        $score = $this->getValidScore();
        $score->setComplete(false);

        $listener->score_create(new ScoreEvent($score));
    }
    public function testCreateScoreNotAccepted() {
        list($listener, $manager) = $this->createListener();

        $manager->expects($this->never())
            ->method('updateHandicap');

        $score = $this->getValidScore();
        $score->setDateAccepted(null);

        $listener->score_create(new ScoreEvent($score));
    }
    public function testCreateScoreAcceptedInFuture() {
        list($listener, $manager) = $this->createListener();

        $manager->expects($this->never())
            ->method('updateHandicap');

        $score = $this->getValidScore();
        $score->setDateAccepted(new \DateTime("+1 year"));

        $listener->score_create(new ScoreEvent($score));
    }

    public function testUpdateScoreValid() {
        list($listener, $manager, $doctrine) = $this->createListener();

        $repo = $this->getRepository($doctrine, 'AppBundle:PersonHandicap', true);
        $repo->expects($this->once())
            ->method('exists')->willReturn(0);

        $manager->expects($this->once())
            ->method('updateHandicap');

        $score = $this->getValidScore();
        $listener->score_update(new ScoreEvent($score));
    }

    public function testUpdateScoreExisting() {
        list($listener, $manager, $doctrine) = $this->createListener();

        $repo = $this->getRepository($doctrine, 'AppBundle:PersonHandicap', true);
        $repo->expects($this->once())
            ->method('exists')->willReturn(1);

        $manager->expects($this->never())
            ->method('updateHandicap');

        $score = $this->getValidScore();
        $listener->score_update(new ScoreEvent($score));
    }
    public function testUpdateScoreIncomplete() {
        list($listener, $manager, $doctrine) = $this->createListener();

        $manager->expects($this->never())
            ->method('updateHandicap');

        $repo = $this->getRepository($doctrine, 'AppBundle:PersonHandicap', true);
        $repo->expects($this->any())
            ->method('exists')->willReturn(0);

        $score = $this->getValidScore();
        $score->setComplete(false);

        $listener->score_update(new ScoreEvent($score));
    }
    public function testUpdateScoreNotAccepted() {
        list($listener, $manager, $doctrine) = $this->createListener();

        $manager->expects($this->never())
            ->method('updateHandicap');

        $repo = $this->getRepository($doctrine, 'AppBundle:PersonHandicap', true);
        $repo->expects($this->any())
            ->method('exists')->willReturn(0);

        $score = $this->getValidScore();
        $score->setDateAccepted(null);

        $listener->score_update(new ScoreEvent($score));
    }
    public function testUpdateScoreAcceptedInFuture() {
        list($listener, $manager, $doctrine) = $this->createListener();

        $manager->expects($this->never())
            ->method('updateHandicap');

        $repo = $this->getRepository($doctrine, 'AppBundle:PersonHandicap', true);
        $repo->expects($this->any())
            ->method('exists')->willReturn(0);

        $score = $this->getValidScore();
        $score->setDateAccepted(new \DateTime("+1 year"));

        $listener->score_update(new ScoreEvent($score));
    }
}
