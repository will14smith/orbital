<?php

namespace AppBundle\Tests\Services\Leagues\Algorithms;

class HandicapInitialiserTraitTest extends InitialiserTestCase
{
    public function testNoPeople()
    {
        $trait = new HandicapInitialiserTraitStub();

        $result = $trait->init([]);

        $this->assertCount(0, $result);
    }

    public function testOnePerson()
    {
        $trait = new HandicapInitialiserTraitStub();

        $person = $this->getPerson(50);
        $result = $trait->init([$person]);

        $this->assertCount(1, $result);
        $this->assertEquals(1, $person->getInitialPosition());
    }
    public function testOnePersonNoHandicap()
    {
        $trait = new HandicapInitialiserTraitStub();

        $person = $this->getPerson();
        $result = $trait->init([$person]);

        $this->assertCount(1, $result);
        $this->assertEquals(1, $person->getInitialPosition());
    }

    public function testTwoPeopleNoHandicaps()
    {
        $trait = new HandicapInitialiserTraitStub();

        $result = $trait->init([$this->getPerson(), $this->getPerson()]);

        $this->assertCount(2, $result);
    }
    public function testTwoPeopleOneHandicap()
    {
        $trait = new HandicapInitialiserTraitStub();

        $person1 = $this->getPerson(50);
        $person2 = $this->getPerson();
        $result = $trait->init([$person1, $person2]);

        $this->assertCount(2, $result);
        $this->assertEquals(2, $person1->getInitialPosition());
        $this->assertEquals(1, $person2->getInitialPosition());

        $person1 = $this->getPerson();
        $person2 = $this->getPerson(50);
        $result = $trait->init([$person1, $person2]);

        $this->assertCount(2, $result);
        $this->assertEquals(1, $person1->getInitialPosition());
        $this->assertEquals(2, $person2->getInitialPosition());
    }
    public function testTwoPeopleDifferentHandicaps()
    {
        $trait = new HandicapInitialiserTraitStub();

        $person1 = $this->getPerson(50);
        $person2 = $this->getPerson(45);
        $result = $trait->init([$person1, $person2]);

        $this->assertCount(2, $result);
        $this->assertEquals(1, $person1->getInitialPosition());
        $this->assertEquals(2, $person2->getInitialPosition());
    }
    public function testTwoPeopleSameHandicaps()
    {
        $trait = new HandicapInitialiserTraitStub();

        $person1 = $this->getPerson(50);
        $person2 = $this->getPerson(50);
        $result = $trait->init([$person1, $person2]);

        $this->assertCount(2, $result);
    }
}
