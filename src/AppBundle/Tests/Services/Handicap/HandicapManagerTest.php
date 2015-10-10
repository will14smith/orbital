<?php


namespace AppBundle\Tests\Services\Handicap;


use AppBundle\Entity\Person;
use AppBundle\Entity\Round;
use AppBundle\Entity\RoundTarget;
use AppBundle\Entity\Score;
use AppBundle\Services\Enum\ScoreZones;
use AppBundle\Services\Enum\Unit;
use AppBundle\Services\Handicap\HandicapCalculator;
use AppBundle\Services\Handicap\HandicapManager;
use AppBundle\Tests\Services\ServiceTestCase;

class HandicapManagerTest extends ServiceTestCase
{
    private function getRound()
    {
        $round = new Round();
        $target = new RoundTarget();

        $round->addTarget($target);

        $target->setScoringZones(ScoreZones::METRIC);
        $target->setArrowCount(60);
        $target->setDistanceValue(18);
        $target->setDistanceUnit(Unit::METER);
        $target->setTargetValue(40);
        $target->setTargetUnit(Unit::CENTIMETER);

        return $round;
    }

    // score 3 = out of range, high. score 4 = avg of 0,1,2. score 5 = low
    private function getScore(Person $person, $idx)
    {
        $score = new Score();

        $score->setPerson($person);
        $score->setRound($this->getRound());

        switch ($idx) {
            case 0:
                $score->setScore(150);
                break;
            case 1:
                $score->setScore(200);
                break;
            case 2:
                $score->setScore(250);
                break;
            case 3:
                $score->setScore(500);
                break;
            case 4:
                $score->setScore(200);
                break;
            case 5:
                $score->setScore(100);
                break;
        }

        if ($idx == 3) {
            $score->setDateShot(new \DateTime('+1 year'));
        } else {
            $score->setDateShot(new \DateTime('yesterday'));
        }

        return $score;
    }

    private function getPerson($score_idxs)
    {
        $person = new Person();

        $scores = array_map(function ($idx) use ($person) {
            return $this->getScore($person, $idx);
        }, $score_idxs);

        $score = null;
        if (count($scores) > 0) {
            $score = $scores[count($scores) - 1];
        }

        $person->scores = $scores;

        return [$person, $score];
    }

    private function expectHandicap($person, $handicap = null)
    {
        $doctrine = $this->getDoctrine();
        $manager = new HandicapManager($doctrine, new HandicapCalculator());

        if ($handicap === null) {
            $count = $this->never();
        } else {
            $count = $this->once();
        }

        $em = $doctrine->getManager();
        $em->expects($count)
            ->method('persist')
            ->with($this->attributeEqualTo('handicap', $handicap));

        $repository = $this->getRepository($doctrine, 'AppBundle:Score', '\AppBundle\Entity\ScoreRepository');
        $repository->expects($this->any())
            ->method('findBy')->willReturn($person->scores);

        $repository->expects($this->any())
            ->method('findByPersonAndLocation')->willReturnCallback(function ($_, $indoor) use ($person) {
                return array_filter($person->scores, function (Score $x) use ($indoor) {
                    return $x->isIndoor() === $indoor;
                });
            });

        $repository->expects($this->any())
            ->method('getScoresByPersonBetween')->willReturnCallback(function ($_, $start, $end) use ($person) {
                return array_filter($person->scores, function (Score $x) use ($start, $end) {
                    return $x->getDateShot() >= $start && $x->getDateShot() <= $end;
                });
            });

        return $manager;
    }

    public function testInitialScoreTooFew()
    {
        // try 1 score
        list($person, $score) = $this->getPerson([0]);
        $this->expectHandicap($person)->updateHandicap($score);

        // try 2 score
        list($person, $score) = $this->getPerson([0, 1]);
        $this->expectHandicap($person)->updateHandicap($score);
    }

    public function testInitialScore3()
    {
        list($person, $score) = $this->getPerson([0, 1, 2]);
        $this->expectHandicap($person, 72)->updateHandicap($score);
    }

    public function testUpdateScoreImprovement()
    {
        list($person, $score) = $this->getPerson([0, 1, 2, 3]);
        $this->expectHandicap($person, 55)->updateHandicap($score);
    }

    public function testUpdateScoreNoChange()
    {
        list($person, $score) = $this->getPerson([0, 1, 2, 4]);
        $this->expectHandicap($person, 72)->updateHandicap($score);
    }

    public function testUpdateScoreWorse()
    {
        list($person, $score) = $this->getPerson([0, 1, 2, 5]);
        $this->expectHandicap($person, 72)->updateHandicap($score);
    }

    public function testReassessScoreTooFewScores()
    {
        list($person) = $this->getPerson([]);
        $this->expectHandicap($person)->reassess($person);

        list($person) = $this->getPerson([0]);
        $this->expectHandicap($person)->reassess($person);

        list($person) = $this->getPerson([0, 1]);
        $this->expectHandicap($person)->reassess($person);
    }

    public function testReassessScoreCorrect()
    {
        list($person) = $this->getPerson([0, 1, 2, 4]);
        $this->expectHandicap($person, 70)->reassess($person);
    }

    public function testReassessScoreTooFewFilteredScores()
    {
        list($person) = $this->getPerson([0, 1, 3]);
        $this->expectHandicap($person)->reassess($person);
    }

    public function testReassessScoreCorrectFiltered()
    {
        list($person) = $this->getPerson([1, 2, 3, 4]);
        $this->expectHandicap($person, 70)->reassess($person);
    }
}
