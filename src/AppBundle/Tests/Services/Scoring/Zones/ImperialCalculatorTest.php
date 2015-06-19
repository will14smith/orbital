<?php


namespace AppBundle\Tests\Services\Scoring\Zones;


use AppBundle\Entity\ScoreArrow;
use AppBundle\Services\Scoring\Zones\ImperialCalculator;
use AppBundle\Tests\Services\ServiceTestCase;

class ImperialCalculatorTest extends ServiceTestCase {
    /**
     * @dataProvider validScores
     */
    public function testValid($score, $expectedValue, $expectedHit, $expectedGold)
    {
        $calculator = new ImperialCalculator();

        $arrow = new ScoreArrow();
        $arrow->setValue($score);

        $this->assertEquals($expectedValue, $calculator->getValue($arrow));
        $this->assertEquals($expectedHit, $calculator->isHit($arrow));
        $this->assertEquals($expectedGold, $calculator->isGold($arrow));
    }

    public function validScores() {
        return [
          ['9', 9, true, true],
          ['7', 7, true, false],
          ['5', 5, true, false],
          ['3', 3, true, false],
          ['1', 1, true, false],
          ['M', 0, false, false],
        ];
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetValueInvalidString()
    {
        $calculator = new ImperialCalculator();

        $arrow = new ScoreArrow();
        $arrow->setValue('Z');

        $calculator->getValue($arrow);
    }
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testIsHitInvalidString()
    {
        $calculator = new ImperialCalculator();

        $arrow = new ScoreArrow();
        $arrow->setValue('Z');

        $calculator->isHit($arrow);
    }
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testIsGoldInvalidString()
    {
        $calculator = new ImperialCalculator();

        $arrow = new ScoreArrow();
        $arrow->setValue('Z');

        $calculator->isGold($arrow);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetValueInvalidNumber()
    {
        $calculator = new ImperialCalculator();

        $arrow = new ScoreArrow();
        $arrow->setValue('11');

        $calculator->getValue($arrow);
    }
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testIsHitInvalidNumber()
    {
        $calculator = new ImperialCalculator();

        $arrow = new ScoreArrow();
        $arrow->setValue('11');

        $calculator->isHit($arrow);
    }
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testIsGoldInvalidNumber()
    {
        $calculator = new ImperialCalculator();

        $arrow = new ScoreArrow();
        $arrow->setValue('11');

        $calculator->isGold($arrow);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetValueInvalidEvenNumber()
    {
        $calculator = new ImperialCalculator();

        $arrow = new ScoreArrow();
        $arrow->setValue('8');

        $calculator->getValue($arrow);
    }
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testIsHitInvalidEvenNumber()
    {
        $calculator = new ImperialCalculator();

        $arrow = new ScoreArrow();
        $arrow->setValue('8');

        $calculator->isHit($arrow);
    }
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testIsGoldInvalidEvenNumber()
    {
        $calculator = new ImperialCalculator();

        $arrow = new ScoreArrow();
        $arrow->setValue('8');

        $calculator->isGold($arrow);
    }
}
