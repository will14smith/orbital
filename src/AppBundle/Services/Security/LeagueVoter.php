<?php

namespace AppBundle\Services\Security;

use AppBundle\Entity\League;
use AppBundle\Entity\Person;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class LeagueVoter extends BaseVoter
{
    protected $attributes = [SecurityAction::SIGNUP, SecurityAction::SUBMIT];
    protected $class = 'AppBundle\Entity\League';

    /**
     * @inheritdoc
     */
    protected function voteInternal(Person $user, $match, $permission)
    {
        /** @var League $match */

        if ($user->isAdmin()) {
            return VoterInterface::ACCESS_GRANTED;
        }

        switch ($permission) {
            case SecurityAction::SIGNUP:
                if($match->canSignup($user) && !$match->isSignedUp($user)) {
                    return VoterInterface::ACCESS_GRANTED;
                }
                break;
            case SecurityAction::SUBMIT:
                if($match->isSignedUp($user)) {
                    return VoterInterface::ACCESS_GRANTED;
                }
                break;
        }

        return VoterInterface::ACCESS_DENIED;
    }
}
