<?php

namespace AppBundle\Services\Security;

use AppBundle\Entity\Person;
use AppBundle\Entity\Score;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class ScoringVoter extends BaseVoter
{
    protected $attributes = [SecurityAction::JUDGE, SecurityAction::SCORE, SecurityAction::SIGN];
    protected $class = 'AppBundle\Entity\Score';

    /**
     * @inheritdoc
     */
    public function voteInternal(Person $user, $score, $permission)
    {
        /** @var Score $score */

        // can't do much with an accepted score
        if ($score->getDateAccepted()) {
            return VoterInterface::ACCESS_DENIED;
        }

        if ($user->isAdmin()) {
            return VoterInterface::ACCESS_GRANTED;
        }

        switch ($permission) {
            case SecurityAction::JUDGE:

                break;
            case SecurityAction::SCORE:
                if ($score->getPerson()->getId() == $user->getId()) {
                    return VoterInterface::ACCESS_GRANTED;
                }

                break;
            case SecurityAction::SIGN:
                if ($score->getPerson()->getId() == $user->getId()) {
                    return VoterInterface::ACCESS_GRANTED;
                }

                break;
        }

        return VoterInterface::ACCESS_DENIED;
    }
}
