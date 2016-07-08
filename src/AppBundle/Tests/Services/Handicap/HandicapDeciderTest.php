<?php

namespace AppBundle\Tests\Services\Handicap;

use AppBundle\Entity\Person;
use AppBundle\Entity\PersonHandicap;
use AppBundle\Entity\Round;
use AppBundle\Entity\RoundTarget;
use AppBundle\Entity\Score;
use AppBundle\Services\Enum\BowType;
use AppBundle\Services\Enum\HandicapType;
use AppBundle\Services\Enum\ScoreZones;
use AppBundle\Services\Enum\Unit;
use AppBundle\Services\Handicap\HandicapCalculator;
use AppBundle\Services\Handicap\HandicapDecider;
use AppBundle\Services\Handicap\HandicapIdentifier;

class HandicapDeciderTest extends \PHPUnit_Framework_TestCase
{
    public function testNoScores()
    {
        // this invokes a special case where reassessment check happens
        $decider = new HandicapDecider($this->getCalculator(), $this->getReassessmentRepository([]));

        $scores = [];

        // initial
        $result = $decider->decide($this->getHandicapId(), null, $scores);

        $this->assertFalse($result->hasHandicap());
        $this->assertEmpty($result->getRemainingScores());

        // update
        $result = $decider->decide($this->getHandicapId(), $this->getHandicap(80), $scores);

        $this->assertFalse($result->hasHandicap());
        $this->assertEmpty($result->getRemainingScores());
    }

    public function testInitialScore()
    {
        $decider = new HandicapDecider($this->getCalculator(), $this->getReassessmentRepository());

        $scores = $this->getScores(100, 150, 200);
        $result = $decider->decide($this->getHandicapId(), null, $scores);

        $this->assertTrue($result->hasHandicap());
        $this->assertEmpty($result->getRemainingScores());

        $handicap = $result->getHandicap();

        $this->assertEquals($handicap->getType(), HandicapType::INITIAL);
        $this->assertEquals($handicap->getHandicap(), 77);
    }

    public function testInitialScoreRemaining()
    {
        $decider = new HandicapDecider($this->getCalculator(), $this->getReassessmentRepository());

        $scores = $this->getScores(100, 150, 200, 250);
        $result = $decider->decide($this->getHandicapId(), null, $scores);

        $this->assertTrue($result->hasHandicap());
        $this->assertCount(1, $result->getRemainingScores());
    }

    public function testInitialScoreTooFew()
    {
        $decider = new HandicapDecider($this->getCalculator(), $this->getReassessmentRepository());

        // test with 1 score
        $scores = $this->getScores(100);
        $result = $decider->decide($this->getHandicapId(), null, $scores);

        $this->assertFalse($result->hasHandicap());
        $this->assertEmpty($result->getRemainingScores());

        // test with 2 scores
        $scores = $this->getScores(100, 150);
        $result = $decider->decide($this->getHandicapId(), null, $scores);

        $this->assertFalse($result->hasHandicap());
        $this->assertEmpty($result->getRemainingScores());
    }

    public function testUpdateImprovement()
    {
        $decider = new HandicapDecider($this->getCalculator(), $this->getReassessmentRepository());

        $scores = $this->getScores(100, 200);
        $result = $decider->decide($this->getHandicapId(), $this->getHandicap(84), $scores);

        $this->assertTrue($result->hasHandicap());
        $this->assertCount(1, $result->getRemainingScores());

        $handicap = $result->getHandicap();

        $this->assertEquals($handicap->getType(), HandicapType::UPDATE);
        $this->assertEquals($handicap->getHandicap(), 83);
    }

    public function testUpdateSame()
    {
        $decider = new HandicapDecider($this->getCalculator(), $this->getReassessmentRepository());

        $scores = $this->getScores(100, 200);
        $result = $decider->decide($this->getHandicapId(), $this->getHandicap(83), $scores);

        $this->assertFalse($result->hasHandicap());
        $this->assertCount(1, $result->getRemainingScores());
    }

    public function testUpdateWorse()
    {
        $decider = new HandicapDecider($this->getCalculator(), $this->getReassessmentRepository());

        $scores = $this->getScores(100, 200);
        $result = $decider->decide($this->getHandicapId(), $this->getHandicap(81), $scores);

        $this->assertFalse($result->hasHandicap());
        $this->assertCount(1, $result->getRemainingScores());
    }

    public function testReassess()
    {
        $decider = new HandicapDecider($this->getCalculator(), $this->getReassessmentRepository($this->getScores(50, 75, 100)));

        $scores = $this->getScores(100, 200);
        $previous_handicap = $this->getHandicap(81);
        $previous_handicap->setDate($this->getSeason(0));

        $result = $decider->decide($this->getHandicapId(), $previous_handicap, $scores);

        $this->assertTrue($result->hasHandicap());
        $this->assertCount(2, $result->getRemainingScores());

        $handicap = $result->getHandicap();

        $this->assertEquals($handicap->getType(), HandicapType::REASSESS);
        $this->assertEquals($handicap->getHandicap(), 86);
    }

    // helpers

    /**
     * @return Round
     */
    public static function getRound()
    {
        $round = new Round();
        $target = new RoundTarget();

        $round->setIndoor(true);
        $round->addTarget($target);

        $target->setScoringZones(ScoreZones::METRIC);
        $target->setArrowCount(60);
        $target->setDistanceValue(18);
        $target->setDistanceUnit(Unit::METER);
        $target->setTargetValue(40);
        $target->setTargetUnit(Unit::CENTIMETER);

        return $round;
    }

    /**
     * @return HandicapCalculator
     */
    public static function getCalculator()
    {
        return new HandicapCalculator();
    }

    /**
     * @param int $value
     *
     * @return Score
     */
    public static function getScore($value)
    {
        $score = new Score();

        $score->setRound(self::getRound());
        $score->setPerson(new Person());
        $score->setDateShot(self::getSeason(1));
        $score->setScore($value);

        return $score;
    }

    /**
     * @param int[] ...$values
     *
     * @return Score[]
     */
    public static function getScores(...$values)
    {
        return array_map([self::class, 'getScore'], $values);
    }

    /**
     * @return HandicapIdentifier
     */
    public static function getHandicapId()
    {
        return new HandicapIdentifier(new Person(), true, BowType::RECURVE);
    }

    /**
     * @param int $value
     *
     * @return PersonHandicap
     */
    public static function getHandicap($value)
    {
        $handicap = new PersonHandicap();
        $handicap->setDate(self::getSeason(1));
        $handicap->setHandicap($value);

        return $handicap;
    }

    /**
     * @param int $year
     *
     * @return \DateTime
     */
    public static function getSeason($year)
    {
        $base_date = new \DateTime('2010-05-01');
        $base_date->add(new \DateInterval('P' . $year . 'Y'));

        return $base_date;
    }

    public static function getReassessmentRepository(array $scores = null)
    {
        if ($scores === null) {
            return new NullReassessmentRepository();
        }

        return new FakeReassessmentRepository($scores);
    }
}
