<?php

namespace AppBundle\Tests\Services\Leagues\Algorithms;

use AppBundle\Entity\LeaguePerson;
use AppBundle\Services\Leagues\Algorithms\StealAlgorithm;

class StealAlgorithmTest extends InitialiserTestCase
{
    private function assertUniqueAndValid($people)
    {
        $positions = array_map(function (LeaguePerson $person) {
            return $person->getInitialPosition();
        }, $people);

        $unique = array_unique($positions);

        $this->assertCount(count($people), $unique, 'the initial positions are not unique');

        foreach ($people as $person) {
            /* @var LeaguePerson $person */
            $this->assertEquals(17, $person->getPoints());
        }
    }

    public function testInitNoPeople()
    {
        $initialiser = new StealAlgorithm(17);

        $result = $initialiser->init([]);

        $this->assertCount(0, $result);
        $this->assertUniqueAndValid($result);
    }

    public function testInitOnePerson()
    {
        $initialiser = new StealAlgorithm(17);

        $person = $this->getPerson();

        $result = $initialiser->init([$person]);

        $this->assertCount(1, $result);
        $this->assertUniqueAndValid($result);
    }

    public function testInitTwoPeople()
    {
        $initialiser = new StealAlgorithm(17);

        $person1 = $this->getPerson();
        $person2 = $this->getPerson();

        $result = $initialiser->init([$person1, $person2]);

        $this->assertCount(2, $result);
        $this->assertUniqueAndValid($result);
    }

    public function testInitThreePeople()
    {
        $initialiser = new StealAlgorithm(17);

        $person1 = $this->getPerson();
        $person2 = $this->getPerson();
        $person3 = $this->getPerson();

        $result = $initialiser->init([$person1, $person2, $person3]);

        $this->assertCount(3, $result);
        $this->assertUniqueAndValid($result);
    }
}
