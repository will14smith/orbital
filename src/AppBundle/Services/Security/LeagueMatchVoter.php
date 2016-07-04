<?php

namespace AppBundle\Services\Security;

use AppBundle\Entity\LeagueMatch;
use AppBundle\Entity\Person;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class LeagueMatchVoter extends BaseVoter
{
    protected $attributes = [SecurityAction::ACCEPT];
    protected $class = 'AppBundle\Entity\LeagueMatch';

    /**
     * {@inheritdoc}
     */
    protected function voteInternal(Person $user, $match, $permission)
    {
        /* @var LeagueMatch $match */

        switch ($permission) {
            case SecurityAction::ACCEPT:
                if ($user->isAdmin() && !$match->getDateConfirmed()) {
                    return VoterInterface::ACCESS_GRANTED;
                }

                break;
        }

        return VoterInterface::ACCESS_DENIED;
    }
}
