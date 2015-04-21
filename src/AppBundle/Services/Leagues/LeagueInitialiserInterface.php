<?php

namespace AppBundle\Services\Leagues;


use AppBundle\Entity\LeaguePerson;

interface LeagueInitialiserInterface {
    /**
     * @param LeaguePerson[] $people
     *
     * @return LeaguePerson[]
     */
    function init(array $people);
}
