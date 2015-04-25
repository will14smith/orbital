<?php


namespace AppBundle\Tests\Services\Leagues\Algorithms;


use AppBundle\Entity\LeagueMatch;
use AppBundle\Entity\LeaguePerson;
use AppBundle\Tests\Services\BaseTestCase;

class ICACScorerTraitTest extends BaseTestCase {
    /**
     * @dataProvider icacScoreData
     */
    public function testScore($delta, $expectedPoints) {
        $scorer = new ICACScorerTraitStub();
        $match = new LeagueMatch();

        $winner = new LeaguePerson();
        $winner->setPoints(100 + $delta);
        $loser = new LeaguePerson();
        $loser->setPoints(100);

        $match->setChallenger($winner);
        $match->setChallengee($loser);
        $match->setAccepted(true);
        $match->setResult(true);

        list($deltaWinner, $deltaLoser) = $scorer->score($match);

        $this->assertEquals($expectedPoints, $deltaWinner);
        $this->assertEquals(0, $deltaLoser);
    }

    public function icacScoreData() {
        // [-20,-19,-15,-14,-10,-9,-4,-3,3,4,9,10,14,15,19,20]
        return [
            [20, 1],
            [19, 4],
            [15, 4],
            [14, 7],
            [10, 7],
            [9, 10],
            [4, 10],
            [3, 13],
            [-3, 13],
            [-4, 16],
            [-9, 16],
            [-10, 19],
            [-14, 19],
            [-15, 22],
            [-19, 22],
            [-20, 25],
        ];
    }
}
