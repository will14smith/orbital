<?php

namespace AppBundle\Tests\Regressions;

use AppBundle\Entity\Round;
use AppBundle\Entity\RoundTarget;
use AppBundle\Entity\Score;
use AppBundle\Services\Enum\BowType;
use AppBundle\Services\Enum\ScoreZones;
use AppBundle\Services\Enum\Unit;
use AppBundle\Services\Handicap\HandicapCalculator;
use PHPUnit_Framework_TestCase;

class Test160 extends PHPUnit_Framework_TestCase
{
    public function testHandicap()
    {
        $wa1440 = new Round();

        $m90 = new RoundTarget();
        $m90->setDistanceValue(90);
        $m90->setDistanceUnit(Unit::METER);
        $m90->setTargetValue(122);
        $m90->setTargetUnit(Unit::CENTIMETER);
        $m90->setArrowCount(36);
        $m90->setEndSize(6);
        $m90->setScoringZones(ScoreZones::METRIC);

        $m70 = new RoundTarget();
        $m70->setDistanceValue(70);
        $m70->setDistanceUnit(Unit::METER);
        $m70->setTargetValue(122);
        $m70->setTargetUnit(Unit::CENTIMETER);
        $m70->setArrowCount(36);
        $m70->setEndSize(6);
        $m70->setScoringZones(ScoreZones::METRIC);

        $m50 = new RoundTarget();
        $m50->setDistanceValue(50);
        $m50->setDistanceUnit(Unit::METER);
        $m50->setTargetValue(80);
        $m50->setTargetUnit(Unit::CENTIMETER);
        $m50->setArrowCount(36);
        $m50->setEndSize(6);
        $m50->setScoringZones(ScoreZones::METRIC);

        $m30 = new RoundTarget();
        $m30->setDistanceValue(30);
        $m30->setDistanceUnit(Unit::METER);
        $m30->setTargetValue(80);
        $m30->setTargetUnit(Unit::CENTIMETER);
        $m30->setArrowCount(36);
        $m30->setEndSize(6);
        $m30->setScoringZones(ScoreZones::METRIC);

        $wa1440->addTarget($m90);
        $wa1440->addTarget($m70);
        $wa1440->addTarget($m50);
        $wa1440->addTarget($m30);

        $score = new Score();

        $score->setRound($wa1440);
        $score->setBowtype(BowType::COMPOUND);
        $score->setScore(1357);

        $calc = new HandicapCalculator();

        $wa1440->setIndoor(false);
        $this->assertEquals(10, $calc->handicapForScore($score));

        $wa1440->setIndoor(true);
        $this->assertEquals(-3, $calc->handicapForScore($score));
    }
}
