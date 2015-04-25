<?php

namespace AppBundle\Services\Leagues\Algorithms;

use AppBundle\Entity\LeaguePerson;

trait HandicapInitialiserTrait
{
    /**
     * @param LeaguePerson[] $people
     *
     * @return LeaguePerson[]
     */
    public function init(array $people)
    {
        usort($people, function(LeaguePerson $personA, LeaguePerson $personB) {
            $handicapA = $personA->getPerson()->getCurrentHandicap();
            $handicapB = $personB->getPerson()->getCurrentHandicap();

            if(!$handicapA && !$handicapB) {
                return 0;
            }
            if(!$handicapA) {
                return -1;
            }
            if(!$handicapB) {
                return 1;
            }

            return $handicapB->getHandicap() - $handicapA->getHandicap();
        });

        $position = 1;
        foreach($people as $person) {
            $person->setInitialPosition($position++);
        }

        return $people;
    }
}
