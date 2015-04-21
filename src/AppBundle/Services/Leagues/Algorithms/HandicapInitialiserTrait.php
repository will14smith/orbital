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
        usort($people, function(LeaguePerson $a, LeaguePerson $b) {
            $ah = $a->getPerson()->getCurrentHandicap();
            $bh = $b->getPerson()->getCurrentHandicap();

            if(!$ah && !$bh) {
                return 0;
            }
            if(!$ah) {
                return 1;
            }
            if(!$bh) {
                return -1;
            }

            return $ah->getHandicap() - $bh->getHandicap();
        });

        $i = 1;
        foreach($people as $person) {
            $person->setInitialPosition($i++);
        }

        return $people;
    }
}
