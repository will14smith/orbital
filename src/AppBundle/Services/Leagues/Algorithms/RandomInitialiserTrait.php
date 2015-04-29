<?php

namespace AppBundle\Services\Leagues\Algorithms;


trait RandomInitialiserTrait {
    /**
     * @param \AppBundle\Entity\LeaguePerson[] $people
     *
     * @return \AppBundle\Entity\LeaguePerson[]
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
