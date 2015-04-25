<?php


namespace AppBundle\Tests\Services\Scoring;


use AppBundle\Entity\Round;
use AppBundle\Entity\RoundTarget;
use AppBundle\Entity\Score;
use AppBundle\Entity\ScoreArrow;
use AppBundle\Services\Enum\ScoreZones;
use AppBundle\Services\Scoring\ScoringCalculator;
use AppBundle\Tests\Services\BaseTestCase;

class ScoringCalculatorTest extends BaseTestCase
{
    /**
     * @dataProvider getArrowsByTargetData
     */
    public function testGetArrowsByTarget($targets, $arrows, $expectedCounts)
    {
        $results = ScoringCalculator::getArrowsByTarget($targets, $arrows);

        $expectedCount = count($expectedCounts);
        $this->assertCount($expectedCount, $results);
        for ($i = 0; $i < $expectedCount; $i++) {
            $this->assertEquals($expectedCounts[$i], count($results[$i]->getArrows()));
        }
    }

    public function getArrowsByTargetData()
    {
        return [
            [$this->targets(0), $this->arrows(0), []],
            [$this->targets(1), $this->arrows(0), [0]],
            [$this->targets(2), $this->arrows(0), [0, 0]],

            [$this->targets(1), $this->arrows(5), [5]],
            [$this->targets(2), $this->arrows(5), [5, 0]],

            [$this->targets(1), $this->arrows(60), [60]],
            [$this->targets(2), $this->arrows(100), [60, 40]],

            [$this->targets(0), $this->arrows(200), []],
            [$this->targets(1), $this->arrows(200), [60]],
            [$this->targets(2), $this->arrows(200), [60, 40]],
        ];
    }

    private function targets($count)
    {
        $result = [];

        if ($count > 0) {
            $target = new RoundTarget();

            $target->setArrowCount(60);
            $target->setScoringZones(ScoreZones::METRIC);

            $result[] = $target;
        }

        if ($count > 1) {
            $target = new RoundTarget();

            $target->setArrowCount(40);
            $target->setScoringZones(ScoreZones::METRIC);

            $result[] = $target;
        }

        return $result;
    }

    private function arrows($count)
    {
        $result = [];

        for ($i = 0; $i < $count; $i++) {
            $arrow = new ScoreArrow();

            if (($i % 2) == 0) {
                $arrow->setValue('X');
            } else if (($i % 5) == 3) {
                $arrow->setValue('5');
            } else {
                $arrow->setValue('M');
            }

            $result[] = $arrow;
        }

        return $result;
    }

    public function testMapArrowsToResultNone()
    {
        $target = $this->targets(1)[0];
        $arrows = $this->arrows(0);

        $result = ScoringCalculator::mapArrowsToResult($target, $arrows);

        $this->assertEquals(0, $result->getTotal());
        $this->assertEquals(0, $result->getGolds());
        $this->assertEquals(0, $result->getHits());
        $this->assertEquals(0, $result->getArrows());
    }

    public function testMapArrowsToResultTooFew()
    {
        $target = $this->targets(1)[0];
        $arrows = $this->arrows(5);

        $result = ScoringCalculator::mapArrowsToResult($target, $arrows);

        $this->assertEquals(35, $result->getTotal());
        $this->assertEquals(3, $result->getGolds());
        $this->assertEquals(4, $result->getHits());
        $this->assertEquals(5, $result->getArrows());
    }

    public function testMapArrowsToResultCorrect()
    {
        $target = $this->targets(1)[0];
        $arrows = $this->arrows(60);

        $result = ScoringCalculator::mapArrowsToResult($target, $arrows);

        $this->assertEquals(330, $result->getTotal());
        $this->assertEquals(30, $result->getGolds());
        $this->assertEquals(36, $result->getHits());
        $this->assertEquals(60, $result->getArrows());
    }

    public function testMapArrowsToResultTooMany()
    {
        $target = $this->targets(1)[0];
        $arrows = $this->arrows(100);

        $result = ScoringCalculator::mapArrowsToResult($target, $arrows);

        $this->assertEquals(330, $result->getTotal());
        $this->assertEquals(30, $result->getGolds());
        $this->assertEquals(36, $result->getHits());
        $this->assertEquals(60, $result->getArrows());
    }

    public function testGetScore()
    {
        $targets = $this->targets(2);
        $arrows = $this->arrows(100);

        $round = new Round();
        $round->addTarget($targets[0]);
        $round->addTarget($targets[1]);

        $score = new Score();
        $score->setRound($round);
        foreach ($arrows as $arrow) {
            $score->addArrow($arrow);
        }

        $result = ScoringCalculator::getScore($score);

        $this->assertEquals(550, $result->getTotal());
        $this->assertEquals(50, $result->getGolds());
        $this->assertEquals(60, $result->getHits());
        $this->assertEquals(100, $result->getArrows());
    }
}
