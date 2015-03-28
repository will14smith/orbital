<?php

namespace AppBundle\Services\Leagues\Algorithms;

use AppBundle\Entity\LeaguePerson;

trait RandomInitialiserTrait {
    /**
     * @param LeaguePerson[] $people
     *
     * @return LeaguePerson[]
     */
    public function init(array $people) {
        shuffle($people);

        $i = 1;
        foreach($people as $person) {
            $person->setInitialPosition($i++);
        }

        return $people;
    }
}