<?php

namespace AppBundle\Tests\Services\Leagues\Algorithms;

use AppBundle\Entity\LeaguePerson;

class RandomInitialiserTraitTest extends InitialiserTestCase
{
    private function assertUnique($people)
    {
        $positions = array_map(function (LeaguePerson $person) {
            return $person->getInitialPosition();
        }, $people);

        $unique = array_unique($positions);

        $this->assertCount(count($people), $unique, 'the initial positions are not unique');
    }

    public function testInitNoPeople()
    {
        $initialiser = new RandomInitialiserTraitStub();

        $result = $initialiser->init([]);

        $this->assertCount(0, $result);
        $this->assertUnique($result);
    }

    public function testInitOnePerson()
    {
        $initialiser = new RandomInitialiserTraitStub();

        $person = $this->getPerson();

        $result = $initialiser->init([$person]);

        $this->assertCount(1, $result);
        $this->assertUnique($result);
    }

    public function testInitTwoPeople()
    {
        $initialiser = new RandomInitialiserTraitStub();

        $person1 = $this->getPerson();
        $person2 = $this->getPerson();

        $result = $initialiser->init([$person1, $person2]);

        $this->assertCount(2, $result);
        $this->assertUnique($result);
    }

    public function testInitThreePeople()
    {
        $initialiser = new RandomInitialiserTraitStub();

        $person1 = $this->getPerson();
        $person2 = $this->getPerson();
        $person3 = $this->getPerson();

        $result = $initialiser->init([$person1, $person2, $person3]);

        $this->assertCount(3, $result);
        $this->assertUnique($result);
    }
}
