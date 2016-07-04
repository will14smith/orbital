<?php

namespace AppBundle\Tests\Services\Handicap;

use AppBundle\Entity\Round;
use AppBundle\Entity\RoundTarget;
use AppBundle\Entity\Score;
use AppBundle\Services\Enum\BowType;
use AppBundle\Services\Enum\ScoreZones;
use AppBundle\Services\Enum\Unit;
use AppBundle\Services\Handicap\HandicapCalculator;

class HandicapCalculatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Round[]
     */
    protected static $rounds;

    public static function setUpBeforeClass()
    {
        self::$rounds = self::getRounds();
    }

    public static function getRounds()
    {
        /** @var Round[] $rounds */
        $rounds = [
            new Round(),
            new Round(),
            new Round(),
        ];

        $target1 = new RoundTarget();
        $target2 = new RoundTarget();

        $target1->setScoringZones(ScoreZones::METRIC);
        $target1->setArrowCount(60);
        $target1->setDistanceValue(18);
        $target1->setDistanceUnit(Unit::METER);
        $target1->setTargetValue(40);
        $target1->setTargetUnit(Unit::CENTIMETER);

        $target2->setScoringZones(ScoreZones::METRIC);
        $target2->setArrowCount(60);
        $target2->setDistanceValue(25);
        $target2->setDistanceUnit(Unit::METER);
        $target2->setTargetValue(60);
        $target2->setTargetUnit(Unit::CENTIMETER);

        $rounds[0]->addTarget($target1);

        $rounds[1]->addTarget($target1);
        $rounds[1]->addTarget($target2);

        return $rounds;
    }

    /**
     * @dataProvider scoreData
     */
    public function testScore($round, $compound, $handicap, $expectedScore)
    {
        $calc = new HandicapCalculator();

        $actualScore = $calc->score($round, $compound, $handicap);

        $this->assertEquals($expectedScore, $actualScore);
    }

    public function scoreData()
    {
        $rounds = self::getRounds();

        return [
            [$rounds[0], false, 1, 595],
            [$rounds[0], true, 1, 574],
            [$rounds[0], false, 15, 574],
            [$rounds[0], true, 15, 554],
            [$rounds[0], false, 30, 530],
            [$rounds[0], true, 30, 520],
            [$rounds[0], false, 70, 205],
            [$rounds[0], true, 70, 205],
            [$rounds[0], false, 100, 17],
            [$rounds[0], true, 100, 17],

            [$rounds[1], false, 1, 1191],
            [$rounds[1], true, 1, 1148],
            [$rounds[1], false, 15, 1151],
            [$rounds[1], true, 15, 1108],
            [$rounds[1], false, 30, 1065],
            [$rounds[1], true, 30, 1043],
            [$rounds[1], false, 70, 413],
            [$rounds[1], true, 70, 412],
            [$rounds[1], false, 100, 28],
            [$rounds[1], true, 100, 28],
        ];
    }

    /**
     * @expectedException \PHPUnit_Framework_Error
     */
    public function testScoreNullRound()
    {
        $calc = new HandicapCalculator();

        $calc->score(null, false, 1);
    }

    /**
     * @expectedException
     */
    public function testScoreLowHandicap()
    {
        $calc = new HandicapCalculator();

        $calc->score(self::$rounds[0], false, 0);
    }

    /**
     * @expectedException
     */
    public function testScoreHighHandicap()
    {
        $calc = new HandicapCalculator();

        $calc->score(self::$rounds[0], false, 101);
    }

    /**
     * @dataProvider handicapData
     */
    public function testHandicap($round, $compound, $score, $expectedHandicap)
    {
        $calc = new HandicapCalculator();

        $actualHandicap = $calc->handicap($round, $compound, $score);

        $this->assertEquals($expectedHandicap, $actualHandicap);
    }

    public function handicapData()
    {
        $scoreData = $this->scoreData();
        $results = [];

        foreach ($scoreData as $score) {
            $results[] = [$score[0], $score[1], $score[3], $score[2]];
            $results[] = [$score[0], $score[1], $score[3] - 1, $score[2] + 1];
        }

        return $results;
    }

    public function testHandicapForScore()
    {
        $calc = new HandicapCalculator();

        $score = new Score();

        $score->setBowtype(BowType::RECURVE);
        $score->setScore(530);
        $score->setRound(self::$rounds[0]);

        $handicap = $calc->handicapForScore($score);

        $this->assertEquals(30, $handicap);
    }
}
