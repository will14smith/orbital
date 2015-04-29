<?php

namespace AppBundle\Services\Security;

use AppBundle\Entity\Competition;
use AppBundle\Entity\Person;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class CompetitionVoter extends BaseVoter
{
    protected $attributes = [SecurityAction::ENTER];
    protected $class = 'AppBundle\Entity\Competition';

    /**
     * @inheritDoc
     */
    protected function voteInternal(Person $user, $competition, $permission)
    {
        /** @var Competition $competition */

        switch ($permission) {
            case SecurityAction::ENTER:
                if ($competition->getInfoOnly()) {
                    return VoterInterface::ACCESS_DENIED;
                }

                if ($user->isAdmin()) {
                    return VoterInterface::ACCESS_GRANTED;
                }

                if ($competition->hasEntered($user)) {
                    return VoterInterface::ACCESS_DENIED;
                }

                if ($competition->getFreeSpaces() > 0) {
                    return VoterInterface::ACCESS_GRANTED;
                }

                break;
        }

        return VoterInterface::ACCESS_DENIED;
    }
}
