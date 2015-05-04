<?php

namespace AppBundle\Tests\Services\Leagues\Algorithms;

use AppBundle\Entity\LeaguePerson;
use AppBundle\Entity\Person;
use AppBundle\Entity\PersonHandicap;
use AppBundle\Tests\Services\ServiceTestCase;

abstract class InitialiserTestCase extends ServiceTestCase
{
    /**
     * @param int $hc
     * @return LeaguePerson
     */
    protected function getPerson($hc = null)
    {
        $leaguePerson = new LeaguePerson();
        $person = new Person();

        if($hc !== null) {
            $handicap = new PersonHandicap();
            $handicap->setHandicap($hc);

            $person->addHandicap($handicap);
        }

        $leaguePerson->setPerson($person);

        return $leaguePerson;
    }
}
