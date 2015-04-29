<?php

namespace AppBundle\Services\Security;

use AppBundle\Entity\Badge;
use AppBundle\Entity\Person;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class BadgeVoter extends BaseVoter
{
    protected $attributes = [SecurityAction::CLAIM];
    protected $class = 'AppBundle\Entity\Badge';

    /**
     * @inheritdoc
     */
    protected function voteInternal(Person $user, $badge, $permission)
    {
        /** @var Badge $badge */

        switch ($permission) {
            case SecurityAction::CLAIM:
                if ($user->isAdmin()) {
                    return VoterInterface::ACCESS_GRANTED;
                }

                if ($badge->getMultiple()) {
                    return VoterInterface::ACCESS_GRANTED;
                }

                return VoterInterface::ACCESS_GRANTED;

                break;
        }

        return VoterInterface::ACCESS_DENIED;
    }
}
