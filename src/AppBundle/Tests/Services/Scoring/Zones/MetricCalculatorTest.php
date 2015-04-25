<?php


namespace AppBundle\Tests\Services\Scoring\Zones;


use AppBundle\Entity\ScoreArrow;
use AppBundle\Services\Scoring\Zones\MetricCalculator;
use AppBundle\Tests\Services\ServiceTestCase;

class MetricCalculatorTest extends ServiceTestCase {
    /**
     * @dataProvider validScores
     */
    public function testValid($score, $expectedValue, $expectedHit, $expectedGold)
    {
        $calculator = new MetricCalculator();

        $arrow = new ScoreArrow();
        $arrow->setValue($score);

        $this->assertEquals($expectedValue, $calculator->getValue($arrow));
        $this->assertEquals($expectedHit, $calculator->isHit($arrow));
        $this->assertEquals($expectedGold, $calculator->isGold($arrow));
    }

    public function validScores() {
        return [
          ['X', 10, true, true],
          ['10', 10, true, true],
          ['9', 9, true, false],
          ['8', 8, true, false],
          ['7', 7, true, false],
          ['6', 6, true, false],
          ['5', 5, true, false],
          ['4', 4, true, false],
          ['3', 3, true, false],
          ['2', 2, true, false],
          ['1', 1, true, false],
          ['M', 0, false, false],
        ];
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetValueInvalidString()
    {
        $calculator = new MetricCalculator();

        $arrow = new ScoreArrow();
        $arrow->setValue('Z');

        $calculator->getValue($arrow);
    }
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testIsHitInvalidString()
    {
        $calculator = new MetricCalculator();

        $arrow = new ScoreArrow();
        $arrow->setValue('Z');

        $calculator->isHit($arrow);
    }
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testIsGoldInvalidString()
    {
        $calculator = new MetricCalculator();

        $arrow = new ScoreArrow();
        $arrow->setValue('Z');

        $calculator->isGold($arrow);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetValueInvalidNumber()
    {
        $calculator = new MetricCalculator();

        $arrow = new ScoreArrow();
        $arrow->setValue('11');

        $calculator->getValue($arrow);
    }
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testIsHitInvalidNumber()
    {
        $calculator = new MetricCalculator();

        $arrow = new ScoreArrow();
        $arrow->setValue('11');

        $calculator->isHit($arrow);
    }
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testIsGoldInvalidNumber()
    {
        $calculator = new MetricCalculator();

        $arrow = new ScoreArrow();
        $arrow->setValue('11');

        $calculator->isGold($arrow);
    }
}
