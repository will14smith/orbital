<?php

namespace AppBundle\Services\Leagues;


use AppBundle\Entity\LeaguePerson;

interface LeagueInitialiser {
    /**
     * @param LeaguePerson[] $people
     *
     * @return LeaguePerson[]
     */
    function init(array $people);
}
