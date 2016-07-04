<?php

namespace AppBundle\Services\Leagues\Algorithms;

use AppBundle\Entity\LeaguePerson;
use AppBundle\Entity\Person;

trait HandicapInitialiserTrait
{
    /**
     * @param LeaguePerson[] $people
     *
     * @return LeaguePerson[]
     */
    public function init(array $people)
    {
        usort($people, function (LeaguePerson $personA, LeaguePerson $personB) {
            $handicapA = $this->getHandicap($personA->getPerson());
            $handicapB = $this->getHandicap($personB->getPerson());

            if (!$handicapA && !$handicapB) {
                return 0;
            }
            if (!$handicapA) {
                return -1;
            }
            if (!$handicapB) {
                return 1;
            }

            return $handicapB->getHandicap() - $handicapA->getHandicap();
        });

        $position = 1;
        foreach ($people as $person) {
            $person->setInitialPosition($position++);
        }

        return $people;
    }

    private function getHandicap(Person $person)
    {
        $indoor = $person->getCurrentHandicap(true);
        $outdoor = $person->getCurrentHandicap(false);

        if ($indoor === null) {
            return $outdoor;
        } elseif ($outdoor === null) {
            return $indoor;
        }

        if ($indoor->getHandicap() < $outdoor->getHandicap()) {
            return $indoor;
        } else {
            return $outdoor;
        }
    }
}
