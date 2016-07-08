<?php

namespace AppBundle\Tests\Regressions;


use AppBundle\Entity\PersonHandicap;
use AppBundle\Services\Handicap\HandicapCalculator;
use AppBundle\Services\Handicap\HandicapDecider;
use AppBundle\Services\Handicap\HandicapManager;
use AppBundle\Tests\Services\Handicap\FakeReassessmentRepository;
use AppBundle\Tests\Services\Handicap\HandicapDeciderTest;
use AppBundle\Tests\Services\ServiceTestCase;


// initial handicap wasn't created
class Test168 extends ServiceTestCase
{
    public function testInitial()
    {
        $doctrine = $this->getDoctrine();
        $decider = new HandicapDecider(new HandicapCalculator(), new FakeReassessmentRepository([]));

        $manager = new HandicapManager($doctrine, $decider);

        $score1 = HandicapDeciderTest::getScore(100); // 82
        $score2 = HandicapDeciderTest::getScore(110); // 80
        $score3 = HandicapDeciderTest::getScore(130); // 78

        $repository = $this->getRepository($doctrine, 'AppBundle:Score', '\AppBundle\Entity\ScoreRepository');
        $repository->expects($this->any())->method('getScoresByHandicapId')->willReturn([$score1, $score2, $score3]);

        $repository = $this->getRepository($doctrine, 'AppBundle:PersonHandicap', '\AppBundle\Entity\PersonHandicapRepository');
        $repository->expects($this->any())->method('findCurrent')->willReturn(null);


        // assertion
        $em = $doctrine->getManager();
        $em->expects($this->exactly(1))
            ->method('persist')
            ->with($this->callback(function (PersonHandicap $handicap) {
                return $handicap->getHandicap() == 80;
            }));

        // test
        $manager->updateHandicap($score3);
    }
}
